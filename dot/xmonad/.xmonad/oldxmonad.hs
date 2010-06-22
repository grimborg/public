{-# LANGUAGE FlexibleInstances, MultiParamTypeClasses, TypeSynonymInstances, FlexibleContexts, NoMonomorphismRestriction #-}
-- Workspaces:
-- 1: code
-- 2: editor
-- 3: web (Firefox)
-- 4: gimp (Gimp)
-- 5: term
-- 6: files
-- 7: music (Spotify)
-- 8: fullscreen
-- 9: im  (pidgin, skype)
--
-- Uses Xmobar with config ~/.xmobarrc
--
-- All mappings use mod4 aka windows aka command, which I have mapped to CapsLock for convenience :)
-- 
-- mod4+shift+c     kill
-- mod4+space       next layout
-- mod4+shift+n     refresh
-- mod4+m           swap master
-- mod4+j           focus down
-- mod4+k           focus up
-- mod4+shift+j     swap down
-- mod4+shift+k     swap up
-- mod4+h           shrink
-- mod4+l           expand
-- mod4+g           toggle window border
-- mod4+t           sink
-- mod4+w           increase number of columns
-- mod4+v           decrease number of columns
-- mod4+q           restart Xmonad
-- mod4+shift+q     close gnome session
-- alt+ctrl+left    previous workspace
-- alt+ctrl+right   next workspace
-- mod4+return      terminal
-- mod4+v           gvim
-- mod4+f           firefox
-- mod4+p           gnome-do
-- mod4+n           nautilus file manager
-- mod4+shift+l     lock screen
-- mod4+o           next screen
-- mod4+shift+o     shift to next screen
-- mod4+alt+o       swap screens
-- mod4+1..9        switch to workspace
-- mod4+shift+1..9  shift to workspace
 
 
import XMonad
import XMonad.Util.EZConfig
import XMonad.Hooks.UrgencyHook
import XMonad.Hooks.DynamicLog
import XMonad.Util.Run(spawnPipe)
import System.IO
import XMonad.Layout.IM
import qualified XMonad.StackSet as S
import XMonad.Actions.CycleWS
import XMonad.Config.Gnome
import XMonad.Hooks.EwmhDesktops
import XMonad.Hooks.ManageDocks
import XMonad.Hooks.ManageHelpers
import XMonad.Layout.Combo
import XMonad.Layout.Grid
import XMonad.Layout.LayoutModifier
import XMonad.Layout.Named
import XMonad.Layout.NoBorders
import XMonad.Layout.PerWorkspace
import XMonad.Layout.Reflect
import XMonad.Layout.TwoPane
import XMonad.Layout.Grid
import XMonad.Layout.WindowNavigation
import XMonad.Util.WindowProperties
import XMonad.Actions.NoBorders
import Control.Monad
import Data.Ratio
import qualified Data.Map as M
 
-- defaults on which we build
-- use e.g. defaultConfig or gnomeConfig
myBaseConfig = gnomeConfig

-- display
-- replace the bright red border with a more stylish colour
myBorderWidth = 2
myNormalBorderColor = "#202030"
myFocusedBorderColor = "#A0A0D0"
 
-- workspaces
myWorkspaces = ["code", "editor", "web", "mail", "gimp", "files", "music", "fullscreen", "im"]
    where terms = map (("term" ++) . show) . (flip take) [1..]
isFullscreen = (== "fullscreen")
 
-- layouts
basicLayout = Tall nmaster delta ratio where
    nmaster = 1
    delta   = 3/100
    ratio   = 1/2
tallLayout = named "tall" $ avoidStruts $ smartBorders basicLayout
wideLayout = named "wide" $ avoidStruts $ Mirror basicLayout
singleLayout = named "single" $ avoidStruts $ smartBorders Full
gridLayout = named "grid" $ avoidStruts $ smartBorders Grid
fullscreenLayout = named "fullscreen" $ noBorders Full
imLayout = avoidStruts $ reflectHoriz $ withIMs ratio rosters chatLayout where
    chatLayout      = Grid
    ratio           = 1%6
    rosters         = [skypeRoster, pidginRoster]
    pidginRoster    = And (ClassName "Pidgin") (Role "buddy_list")
    skypeRoster     = (ClassName "Skype") `And` (Not (Title "Options")) `And` (Not (Role "Chats")) `And` (Not (Role "CallWindowForm"))
 
gimpLayout = withIM (0.11) (Role "gimp-toolbox") $
             reflectHoriz $
             withIM (0.15) (Role "gimp-dock") Full

myLayoutHook = smartBorders $ fullscreen $ im $ gimp $ normal where
    normal     = tallLayout ||| wideLayout ||| gridLayout ||| singleLayout
    fullscreen = onWorkspace "fullscreen" fullscreenLayout
    im         = onWorkspace "im" imLayout
    gimp       = onWorkspace "gimp" gimpLayout
 
-- special treatment for specific windows:
-- put the Pidgin and Skype windows in the im workspace
myManageHook = composeAll
    [ className =? "Gimp"      --> doFloat
    , className =? "Vncviewer" --> doFloat
    , resource  =? "Do" --> doIgnore
    , XMonad.Hooks.ManageHelpers.isFullscreen =? True --> doFullFloat
    ] <+> imManageHooks <+> manageHook myBaseConfig

imManageHooks = composeAll [
        isIM    --> moveToIM,
        isWeb   --> moveToWeb,
        isMusic --> moveToMusic,
        isGimp  --> moveToGimp
    ] where
    isIM        = foldr1 (<||>) [isPidgin, isSkype]
    isPidgin    = className =? "Pidgin"
    isSkype     = className =? "Skype"
    moveToIM    = doF $ S.shift "im"
    isWeb       = className =? "Firefox"
    moveToWeb   = doF $ S.shift "web"
    isMusic     = className =? "spotify"
    moveToMusic = doF $ S.shift "music"
    isGimp      = className =? "Gimp-2.6"
    moveToGimp  = doF $ S.shift "gimp"
 
-- Mod4 is the Super / Windows key
myModMask = mod4Mask
altMask = mod1Mask
 
-- better keybindings for dvorak
myKeys conf = M.fromList $
    [ ((myModMask              , xK_Return), spawn $ XMonad.terminal conf)
    , ((myModMask .|. shiftMask, xK_c     ), kill)
    , ((myModMask              , xK_space ), sendMessage NextLayout)
    , ((myModMask .|. shiftMask, xK_n     ), refresh)
    , ((myModMask              , xK_m     ), windows S.swapMaster)
    , ((myModMask              , xK_j     ), windows S.focusDown)
    , ((myModMask              , xK_k     ), windows S.focusUp)
    , ((myModMask .|. shiftMask, xK_j     ), windows S.swapDown)
    , ((myModMask .|. shiftMask, xK_k     ), windows S.swapUp)
    , ((myModMask              , xK_h     ), sendMessage Shrink)
    , ((myModMask              , xK_l     ), sendMessage Expand)
    , ((myModMask              , xK_g     ), withFocused toggleBorder)
    , ((myModMask              , xK_t     ), withFocused $ windows . S.sink)
    , ((myModMask              , xK_w     ), sendMessage (IncMasterN 1))
    , ((myModMask              , xK_v     ), sendMessage (IncMasterN (-1)))
    , ((myModMask              , xK_q     ), broadcastMessage ReleaseResources >> restart "xmonad" True)
    , ((myModMask              , xK_a     ), focusUrgent)
    , ((myModMask .|. shiftMask, xK_q     ), spawn "gnome-session-save --kill")
    , ((altMask .|. controlMask, xK_Left  ), prevWS)
    , ((altMask .|. controlMask, xK_Right ), nextWS)
    , ((mod4Mask, xK_v), spawn "gvim")
    , ((mod4Mask, xK_f), spawn "firefox")
    --, ((mod4Mask, xK_p), spawn "gnome-do")
    , ((mod4Mask, xK_n), spawn "nautilus --no-desktop")
    , ((mod4Mask .|. shiftMask, xK_l), spawn "gnome-screensaver-command -l")
    , ((mod4Mask, xK_o), nextScreen)
    , ((mod4Mask .|. shiftMask, xK_o), shiftNextScreen)
    , ((mod4Mask .|. mod1Mask, xK_o), swapNextScreen)
    ] ++
    [ ((myModMask, k), windows $ S.greedyView i)
        | (i, k) <- zip myWorkspaces workspaceKeys
    ] ++
    -- mod+F1..F10 moves window to workspace and switches to that workspace
    [ (((myModMask .|. shiftMask), k), (windows $ S.shift i) >> (windows $ S.greedyView i))
        | (i, k) <- zip myWorkspaces workspaceKeys
    ]
    where workspaceKeys = [xK_1 .. xK_9]
 
-- mouse bindings that mimic Gnome's
myMouseBindings (XConfig {XMonad.modMask = modMask}) = M.fromList $
    [ ((altMask, button1), (\w -> focus w >> mouseMoveWindow w))
    , ((altMask, button2), (\w -> focus w >> mouseResizeWindow w))
    , ((altMask, button3), (\w -> focus w >> (withFocused $ windows . S.sink)))
    , ((altMask, button4), (const $ windows S.swapUp))
    , ((altMask, button5), (const $ windows S.swapDown))
    ]
 
--myPP statusPipe = xmobarPP {
--    ppOutput  = hPutStrLn statusPipe,
--    ppTitle   = xmobarColor "green"  "" . shorten 60,
--    ppUrgent  = xmobarColor "red" "" . wrap "!" "!"
--}



-- put it all together
main = do
    xmproc <- spawnPipe "xmobar ~/.xmobarrc"
    xmonad $ withUrgencyHook NoUrgencyHook myBaseConfig
        { modMask = myModMask
        , logHook = dynamicLogWithPP $ xmobarPP
            { ppOutput = hPutStrLn xmproc
            , ppTitle = xmobarColor "green" "" . shorten 50
            , ppUrgent  = xmobarColor "red" "" . wrap "!" "!"
            }
        , workspaces = myWorkspaces
        , layoutHook = myLayoutHook
        , manageHook = myManageHook
        , borderWidth = myBorderWidth
        , normalBorderColor = myNormalBorderColor
        , focusedBorderColor = myFocusedBorderColor
        , keys = myKeys
        , mouseBindings = myMouseBindings
        , terminal = "sakura"
        }
 
-- modified version of XMonad.Layout.IM --
 
-- | Data type for LayoutModifier which converts given layout to IM-layout
-- (with dedicated space for the roster and original layout for chat windows)
data AddRosters a = AddRosters Rational [Property] deriving (Read, Show)
 
instance LayoutModifier AddRosters Window where
  modifyLayout (AddRosters ratio props) = applyIMs ratio props
  modifierDescription _                = "IMs"
 
-- | Modifier which converts given layout to IMs-layout (with dedicated
-- space for rosters and original layout for chat windows)
withIMs :: LayoutClass l a => Rational -> [Property] -> l a -> ModifiedLayout AddRosters l a
withIMs ratio props = ModifiedLayout $ AddRosters ratio props
 
-- | IM layout modifier applied to the Grid layout
gridIMs :: Rational -> [Property] -> ModifiedLayout AddRosters Grid a
gridIMs ratio props = withIMs ratio props Grid
 
hasAnyProperty :: [Property] -> Window -> X Bool
hasAnyProperty [] _ = return False
hasAnyProperty (p:ps) w = do
    b <- hasProperty p w
    if b then return True else hasAnyProperty ps w
 
-- | Internal function for placing the rosters specified by
-- the properties and running original layout for all chat windows
applyIMs :: (LayoutClass l Window) =>
               Rational
            -> [Property]
            -> S.Workspace WorkspaceId (l Window) Window
            -> Rectangle
            -> X ([(Window, Rectangle)], Maybe (l Window))
applyIMs ratio props wksp rect = do
    let stack = S.stack wksp
    let ws = S.integrate' $ stack
    rosters <- filterM (hasAnyProperty props) ws
    let n = fromIntegral $ length rosters
    let (rostersRect, chatsRect) = splitHorizontallyBy (n * ratio) rect
    let rosterRects = splitHorizontally n rostersRect
    let filteredStack = stack >>= S.filter (`notElem` rosters)
    wrs <- runLayout (wksp {S.stack = filteredStack}) chatsRect
    return ((zip rosters rosterRects) ++ fst wrs, snd wrs)
