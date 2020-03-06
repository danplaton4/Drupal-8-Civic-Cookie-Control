<?php


namespace Drupal\civiccookiecontrol\Access;

use Drupal\civiccookiecontrol\Form\CivicCookieControlSettings;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

class CookieControlAccess
{
    public function checkAccess(AccountInterface $account)
    {
        return AccessResult::allowedIf($account->hasPermission('administer civiccookiecontrol') && $this->checkApiKey());
    }

    public static function checkApiKey(){
        if (CivicCookieControlSettings::validateApiKey(\Drupal::config('civiccookiecontrol.settings')->get('civiccookiecontrol_api_key')) ==
            \Drupal::config('civiccookiecontrol.settings')->get('civiccookiecontrol_product') ) {
            return true;
        }else{
            return false;
        }

    }
}