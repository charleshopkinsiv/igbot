<?php


namespace IgBot\Actions;

use \IgBot\Account\AccountDriver;
use \IgBot\AccountDriverUtil;

class LikeRecentPost extends Action
{

    private static string   $url_base               = "https://instagram.com/";
    private static string   $recent_post_selector   = "a > div.eLAPa";
    private static string   $first_image_selector   = "#react-root > section > main > div > header > div > div > span > img";
    private static string   $like_btn_selector      = "section > span.fr66n > button.wpO6b";

    protected string        $action_title           = "Like Recent Post";
    protected string        $action_description     = "Will like a users recent post if not already liked.";


    public function execute(AccountDriver $Driver)
    {

        $Driver->get(self::$url_base . $this->val_one . "/");

        try {

            $Driver->waitUntilCssSelector(self::$first_image_selector, 5); // Click First Post

            if($Driver->elementsCssSelector(self::$recent_post_selector)) {
    
                if($Driver->getDebug())
                    printf("\n\tLiking recent post for user: %s\n", $this->val_one);
    
                $Driver->click(self::$recent_post_selector);
    
                $Driver->waitUntilCssSelector(self::$like_btn_selector); // Like First Post
                $Driver->click(self::$like_btn_selector);
    
                $Driver->getLimiter()->increment();

                return true;
            }
        }

        catch(\Exception $e) {

            $Driver->failedTask($this);
        }
    }
}
