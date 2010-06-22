import XMonad
import XMonad.Hooks.DynamicLog
import XMonad.Hooks.ManageDocks
import XMonad.Util.Run(spawnPipe)
import XMonad.Util.EZConfig(additionalKeys)
import System.IO
import XMonad.Layout.Named 
import XMonad.Layout.NoBorders
import XMonad.Util.EZConfig
import qualified XMonad.StackSet as S
import XMonad.Actions.CycleWS
import XMonad.Config.Gnome
import XMonad.Hooks.EwmhDesktops
import XMonad.Hooks.ManageDocks
import XMonad.Layout.Combo
import XMonad.Layout.Grid
import XMonad.Layout.LayoutModifier
import XMonad.Layout.PerWorkspace
import XMonad.Layout.Reflect
import XMonad.Layout.TwoPane
import XMonad.Layout.WindowNavigation
import XMonad.Layout.IM
import XMonad.Util.WindowProperties
import Control.Monad
import Data.Ratio
import XMonad.Layout.Spiral
import Data.Ratio ((%))
import XMonad.Layout.LayoutModifier
import XMonad.Layout.PerWorkspace
import XMonad.Layout.Named
import Control.Monad
import qualified Data.Map as M

myManageHook = composeAll
    [ className =? "Gimp"      --> doFloat
    , className =? "Vncviewer" --> doFloat
    , resource  =? "Do" --> doIgnore
    ] <+> imManageHooks


-- layouts
basicLayout = Tall nmaster delta ratio where
    nmaster = 1
    delta   = 3/100
    ratio   = 1/2
tallLayout = named "tall" $ avoidStruts $ basicLayout
wideLayout = named "wide" $ avoidStruts $ Mirror basicLayout
singleLayout = named "single" $ avoidStruts $ noBorders Full
fullscreenLayout = named "fullscreen" $ noBorders Full
 
myLayoutHook = fullscreen $ normal where
    normal     = tallLayout ||| wideLayout ||| singleLayout ||| spiral (6/7)
    fullscreen = onWorkspace "fullscreen" fullscreenLayout

imManageHooks = composeAll [isIM --> moveToIM] where
    isIM     = foldr1 (<||>) [isPidgin, isSkype]
    isPidgin = className =? "Pidgin"
    isSkype  = className =? "Skype"
    moveToIM = doF $ S.shift "im"

myWorkspaces = ["1:code", "2:code", "3:web", "4:terms", "5", "6:files", "7:vm", "8:music", "9:im"]


main = do
    xmproc <- spawnPipe "/usr/local/bin/xmobar /home/grimborg/.xmobarrc"
    xmonad $ defaultConfig
        { manageHook = manageDocks <+> myManageHook
                        <+> manageHook defaultConfig
        , layoutHook = myLayoutHook
        , logHook = dynamicLogWithPP $ xmobarPP
                        { ppOutput = hPutStrLn xmproc
                        , ppTitle = xmobarColor "green" "" . shorten 50
                        }
        , modMask = mod4Mask     -- Rebind Mod to the Windows key
        , terminal = "sakura"
        , normalBorderColor  = "#dddddd"
        , focusedBorderColor = "#0033ff"
        , workspaces = myWorkspaces
        } `additionalKeys`
        [ ((mod4Mask .|. shiftMask, xK_z), spawn "xscreensaver-command -lock")
        , ((mod4Mask, xK_v), spawn "gvim")
        , ((mod4Mask, xK_f), spawn "firefox")
        , ((mod4Mask, xK_p), spawn "gnome-do")
        , ((mod4Mask, xK_n), spawn "nautilus --no-desktop")
        , ((mod4Mask .|. shiftMask, xK_l), spawn "gnome-screensaver-command -l")
        , ((mod4Mask, xK_o), nextScreen)
        , ((mod4Mask .|. shiftMask, xK_o), shiftNextScreen)
        , ((mod4Mask .|. mod1Mask, xK_o), swapNextScreen)
        ]
 

