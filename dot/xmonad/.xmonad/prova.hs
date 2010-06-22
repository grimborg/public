import Data.String
import Data.Char

myWorkspaces = ["code", "editor", "web", "mail", "gimp", "files", "music", "fullscreen", "im"]

desktopIcon :: String -> String
desktopIcon = icon' myWorkspaces icons
    where icon' :: [String] -> [Char] -> String -> String
          icon' (s:ss) (i:is) d
            | s == d = show "<span face=\"WebDings\">&#" ++ show (ord i) ++ ";</span>" 
            | otherwise = icon' ss is d 
          icons = ['a','ø','ù','¸','õ','¢','Ã','O','¥','_']
