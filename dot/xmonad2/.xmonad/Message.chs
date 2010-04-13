-- HDBus -- Haskell bindings for D-Bus.
-- Copyright (C) 2006 Evan Martin <martine@danga.com>

#include "hdbus.h"

module DBus.Message (
  -- * Messages
  Message,
  messagePtrToMessage,
  messagePtrToMessageIncRef,
  newSignal, newMethodCall,
  -- * Accessors
  MessageType(..),
  getType, getSignature,
  getPath, getInterface, getMember, getErrorName,
  getDestination, getSender,
  args,
  addArgs

{-
  -- ** Dictionaries
  -- | D-Bus functions that expect a dictionary must be passed a 'Dict',
  -- which is trivially constructable from an appropriate list.
  -- ** Variants
  -- | Some D-Bus functions allow variants, which are similar to
  -- 'Data.Dynamic' dynamics but restricted to D-Bus data types.
-}
) where

import Control.Monad (when)
import Data.Int
import Data.Word
import Foreign
import Foreign.C.String
import Foreign.C.Types

import DBus.Internal
{#import DBus.Types.Shared#}
{#import DBus.Shared#}
{#import DBus.Types#}

{#context lib="dbus-1" prefix="dbus"#}

-- |Increase the reference count of a message.
{#fun unsafe message_ref {withMessage* `Message'} -> `()'#}

-- |Decrease the reference count of a message.
foreign import ccall unsafe "&dbus_message_unref"
  message_unref :: FunPtr (Ptr Message -> IO ())

messagePtrToMessageIncRef :: Ptr Message -> IO Message
messagePtrToMessageIncRef pmsg = do
  msg <- messagePtrToMessage pmsg
  message_ref msg
  return msg

messagePtrToMessage :: Ptr Message -> IO Message
messagePtrToMessage msg = do
  throwIfNull "null pointer message" (return msg)
  fmap Message $ newForeignPtr message_unref msg

-- |newSignal serviceName pathName InterfaceName
{#fun unsafe message_new_signal as newSignal
    {`String' ,`String' ,`String'}
    -> `Message' messagePtrToMessage*#}

-- |newMethodCall serviceName pathName interfaceName method
{#fun unsafe message_new_method_call as newMethodCall
    {`String' ,`String' ,`String' ,`String'}
    -> `Message' messagePtrToMessage*#}

{#fun unsafe message_get_type as getType {withMessage* `Message'} -> `MessageType' cToEnum#}

getOptionalString :: (Ptr Message -> IO CString) -> Message -> IO (Maybe String)
getOptionalString getter (Message msg) =
  withForeignPtr msg getter >>= maybePeek peekCString

getPath, getInterface, getMember, getErrorName, getDestination, getSender
  :: Message -> IO (Maybe String)
getPath        = getOptionalString {#call unsafe dbus_message_get_path#}
getInterface   = getOptionalString {#call unsafe dbus_message_get_interface#}
getMember      = getOptionalString {#call unsafe dbus_message_get_member#}
getErrorName   = getOptionalString {#call unsafe dbus_message_get_error_name#}
getDestination = getOptionalString {#call unsafe dbus_message_get_destination#}
getSender      = getOptionalString {#call unsafe dbus_message_get_sender#}
{#fun unsafe message_get_signature as getSignature  {withMessage* `Message'} -> `String'#}


-- |Retrieve the arguments from a message.
args :: (Arg a) => Message -> IO a
args msg =
  allocIter $ \iter -> do
    has_args <- message_iter_init msg iter
    fromIterInternal iter

-- |Add arguments to a message.
addArgs :: (Arg a) => Message -> a -> IO ()
addArgs msg arg =
  allocIter  $ \iter -> do
    message_iter_init_append msg iter
    toIterInternal arg iter

-- vim: set ts=2 sw=2 tw=72 et ft=haskell :
