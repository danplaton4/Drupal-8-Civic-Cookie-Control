<?php

namespace Drupal\civiccookiecontrol\Entity;

use Drupal\civiccookiecontrol\AltLanguageInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Example entity.
 *
 * @ConfigEntityType(
 *   id = "altlanguage",
 *   label = @Translation("Alternative Language"),
 *   handlers = {
 *     "list_builder" =
 *   "Drupal\civiccookiecontrol\Controller\AltLanguageListBuilder",
 *     "form" = {
 *       "add" = "Drupal\civiccookiecontrol\Form\AltLanguageForm",
 *       "edit" = "Drupal\civiccookiecontrol\Form\AltLanguageForm",
 *       "delete" = "Drupal\civiccookiecontrol\Form\AltLanguageDeleteForm",
 *     }
 *   },
 *   config_prefix = "altlanguage",
 *   admin_permission = "administer civiccookiecontrol",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "altLanguageIsoCode",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "edit-form" =
 *   "/admin/config/system/cookiecontrol/altlanguage/{altlanguage}",
 *     "delete-form" =
 *   "/admin/config/system/cookiecontrol/altlanguage/{altlanguage}/delete",
 *   }
 * )
 */
class AltLanguage extends ConfigEntityBase implements AltLanguageInterface
{

    /**
     * The altlanguage ID.
     *
     * @var string
     */
    public $id;

    /**
     * The Alternative Language ISO Code.
     *
     * @var string
     */
    public $altLanguageIsoCode;

    /**
     * The Alternative Language Title.
     *
     * @var string
     */
    public $altLanguageTitle;

    /**
     * The Alternative Language Intro.
     *
     * @var string
     */
    public $altLanguageIntro;

    /**
     * The Alternative Language Necessary Title.
     *
     * @var string
     */
    public $altLanguageNecessaryTitle;

    /**
     * The Alternative Language Necessary Description.
     *
     * @var string
     */
    public $altLanguageNecessaryDescription;


    /**
     * The Alternative Language On Text.
     *
     * @var string
     */
    public $altLanguageOn;

    /**
     * The Alternative Language Off Text.
     *
     * @var string
     */
    public $altLanguageOff;

    /**
     * The notify title in Alternative Language.
     *
     * @var string
     */
    public $altLanguageNotifyTitle;

    /**
     * The notify Description in Alternative Language.
     *
     * @var string
     */
    public $altLanguageNotifyDescription;

    /**
     * The accept text in Alternative Language.
     *
     * @var string
     */
    public $altLanguageAccept;

    /**
     * The accept Recommended text in Alternative Language.
     *
     * @var string
     */
    public $altLanguageAcceptRecommended;

    /**
     * The settings text in Alternative Language.
     *
     * @var string
     */
    public $altLanguageSettings;

    /**
     * The Third party cookie title in Alternative Language.
     *
     * @var string
     */
    public $altLanguageThirdPartyTitle;

    /**
     * The Third party cookie Description in Alternative Language.
     *
     * @var string
     */
    public $altLanguageThirdPartyDescription;

    /**
     * The oprional cookies label in Alternative Language.
     *
     * @var string
     */
    public $altLanguageOptionalCookies;

    /**
     * The Statement Description text in Alternative Language.
     *
     * @var string
     */
    public $altLanguageStmtDescrText;

    /**
     * The Statement Name text in Alternative Language.
     *
     * @var string
     */
    public $altLanguageStmtNameText;

    /**
     * The Statement URL for Alternative Language.
     *
     * @var string
     */
    public $altLanguageStmtUrl;

    /**
     * The Statement Updated Date for Alternative Language.
     *
     * @var string
     */
    public $altLanguageStmtDate;

    public $altLanguageIabLabelText;

    public $altLanguageIabDescriptionText;

    public $altLanguageIabConfigureText;

    public $altLanguageIabPanelTitleText;

    public $altLanguageIabPanelIntroText;

    public $altLanguageIabAboutIabText;

    public $altLanguageIabIabNameText;

    public $altLanguageIabIabLinkText;

    public $altLanguageIabPanelBackText;

    public $altLanguageIabVendorTitleText;

    public $altLanguageIabVendorConfigureText;

    public $altLanguageIabVendorBackText;

    public $altLanguageIabAcceptAllText;

    public $altLanguageIabRejectAllText;

    public $altLanguageIabBackText;

    /**
     * Get the alt language Iso code.
     *
     * @return string
     *   Get the alt language Iso code.
     */
    public function getAltLanguageIsoCode()
    {
        return $this->altLanguageIsoCode;
    }

    /**
     * Set the alt language Iso code.
     *
     * @param string $altLanguageIsoCode
     *   The alt language Iso code.
     */
    public function setAltLanguageIsoCode($altLanguageIsoCode)
    {
        $this->altLanguageIsoCode = $altLanguageIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLanguageTitle()
    {
        return $this->altLanguageTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setAltLanguageTitle($altLanguageTitle)
    {
        $this->altLanguageTitle = $altLanguageTitle;

        return $this->altLanguageTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLanguageNecessaryTitle()
    {
        return $this->altLanguageNecessaryTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setAltLanguageNecessaryTitle($altLanguageNecessaryTitle)
    {
        $this->altLanguageNecessaryTitle = $altLanguageNecessaryTitle;
        return $this->altLanguageNecessaryTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLanguageNecessaryDescription()
    {
        return $this->altLanguageNecessaryDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setAltLanguageNecessaryDescription($altLanguageNecessaryDescription)
    {
        $this->altLanguageNecessaryDescription = $altLanguageNecessaryDescription;
        return $this->altLanguageNecessaryDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLanguageThirdPartyTitle()
    {
        return $this->altLanguageThirdPartyTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setAltLanguageThirdPartyTitle($altLanguageThirdPartyTitle)
    {
        $this->altLanguageThirdPartyTitle = $altLanguageThirdPartyTitle;
        return $this->altLanguageThirdPartyTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLanguageThirdPartyDescription()
    {
        return $this->altLanguageThirdPartyDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setAltLanguageThirdPartyDescription($altLanguageThirdPartyDescription)
    {
        $this->altLanguageThirdPartyDescription = $altLanguageThirdPartyDescription;
        return $this->altLanguageThirdPartyDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getAltLanguageOptionalCookies()
    {
        return $this->altLanguageOptionalCookies;
    }

    /**
     * {@inheritdoc}
     */
    public function setAltLanguageOptionalCookies($altLanguageOptionalCookies)
    {
        $this->altLanguageOptionalCookies = $altLanguageOptionalCookies;
        return $this->altLanguageOptionalCookies;
    }

    /**
     * The Intro text in alt language.
     *
     * @return string
     *   Get the Intro text in alt language.
     */
    public function getAltLanguageIntro()
    {
        return $this->altLanguageIntro;
    }

    /**
     * Set the Intro text in alt language.
     *
     * @param string $altLanguageIntro
     *   The intro text in alt language.
     */
    public function setAltLanguageIntro($altLanguageIntro)
    {
        $this->altLanguageIntro = $altLanguageIntro;
    }

    /**
     * Returns Statement Description Text in Alternative Language.
     *
     * @return string
     *   Statement Description Text in Alternative Language.
     */
    public function getAltLanguageStmtDescrText()
    {
        return $this->altLanguageStmtDescrText;
    }

    /**
     * Sets Statement Description Text in Alternative Language.
     *
     * @param string $altLanguageStmtDescrText
     *   Statement Description Text in Alternative Language.
     *
     * @return string
     *   Statement Description Text in Alternative Language.
     */
    public function setAltLanguageStmtDescrText($altLanguageStmtDescrText)
    {
        $this->altLanguageStmtDescrText = $altLanguageStmtDescrText;

        return $altLanguageStmtDescrText;
    }

    /**
     * Returns Statement Name Text in Alternative Language.
     *
     * @return string
     *   Statement Name Text in Alternative Language.
     */
    public function getAltLanguageStmtNameText()
    {
        return $this->altLanguageStmtNameText;
    }

    /**
     * Sets Statement Name Text in Alternative Language.
     *
     * @param string $altLanguageStmtNameText
     *   Statement Name Text in Alternative Language.
     *
     * @return string
     *   Statement Name Text in Alternative Language.
     */
    public function setAltLanguageStmtNameText($altLanguageStmtNameText)
    {
        $this->altLanguageStmtNameText = $altLanguageStmtNameText;

        return $altLanguageStmtNameText;
    }

    /**
     * Returns Statement URL for Alternative Language.
     *
     * @return string
     *   Statement URL in Alternative Language.
     */
    public function getAltLanguageStmtUrl()
    {
        return $this->altLanguageStmtUrl;
    }

    /**
     * Sets Statement URL in Alternative Language.
     *
     * @param string $altLanguageStmtUrl
     *   Statement URL in Alternative Language.
     *
     * @return string
     *   Statement URL in Alternative Language.
     */
    public function setAltLanguageStmtUrl($altLanguageStmtUrl)
    {
        $this->altLanguageStmtUrl = $altLanguageStmtUrl;

        return $altLanguageStmtUrl;
    }

    /**
     * Returns Statement Updated Date in Alternative Language.
     *
     * @return string
     *   Statement Updated Date in Alternative Language.
     */
    public function getAltLanguageStmtDate()
    {
        return $this->altLanguageStmtDate;
    }

    /**
     * Sets Statement Date in Alternative Language.
     *
     * @param string $altLanguageStmtDate
     *   Statement Date for Alternative Language.
     *
     * @return string
     *   Statement Date in Alternative Language.
     */
    public function setAltLanguageStmtDate($altLanguageStmtDate)
    {
        $this->altLanguageStmtDate = $altLanguageStmtDate;

        return $altLanguageStmtDate;
    }

    /**
     * Returns On Text in Alternative Language.
     *
     * @return string
     *   On Text in Alternative Language.
     */
    public function getAltLanguageOn()
    {
        return $this->altLanguageOn;
    }

    /**
     * Sets On Text in Alternative Language.
     *
     * @param string $altLanguageOn
     *   On text in Alternative Language.
     *
     * @return string
     *   On text in Alternative Language.
     */
    public function setAltLanguageOn($altLanguageOn)
    {
        $this->altLanguageOn = $altLanguageOn;
        return $this->altLanguageOn;
    }

    /**
     * Returns Off Text in Alternative Language.
     *
     * @return string
     *   Off Text in Alternative Language.
     */
    public function getAltLanguageOff()
    {
        return $this->altLanguageOff;
    }

    /**
     * Sets Off Text in Alternative Language.
     *
     * @param string $altLanguageOff
     *   Off text in Alternative Language.
     *
     * @return string
     *   Off text in Alternative Language.
     */
    public function setAltLanguageOff($altLanguageOff)
    {
        $this->altLanguageOff = $altLanguageOff;
        return $this->altLanguageOff;

    }

    /**
     * Returns Notify Title in Alternative Language.
     *
     * @return string
     *   Notify Title in Alternative Language.
     */
    public function getAltLanguageNotifyTitle()
    {
        return $this->altLanguageNotifyTitle;
    }

    /**
     * Sets Notify Title in Alternative Language.
     *
     * @param string $altLanguageNotifyTitle
     *   Notify in Alternative Language.
     *
     * @return string
     *   Notify Title in Alternative Language.
     */
    public function setAltLanguageNotifyTitle($altLanguageNotifyTitle)
    {
        $this->altLanguageNotifyTitle = $altLanguageNotifyTitle;
        return $this->altLanguageNotifyTitle;
    }

    /**
     * Returns Notify Description in Alternative Language.
     *
     * @return string
     *   Notify Description in Alternative Language.
     */
    public function getAltLanguageNotifyDescription()
    {
        return $this->altLanguageNotifyDescription;
    }

    /**
     * Sets Notify Description in Alternative Language.
     *
     * @param string $altLanguageNotifyDescription
     *   Notify Description in Alternative Language.
     *
     * @return string
     *   Notify Description in Alternative Language.
     */
    public function setAltLanguageNotifyDescription($altLanguageNotifyDescription)
    {
        $this->altLanguageNotifyDescription = $altLanguageNotifyDescription;
        return $this->altLanguageNotifyDescription;
    }

    /**
     * Returns Accept Text in Alternative Language.
     *
     * @return string
     *   Accept Text in Alternative Language.
     */
    public function getAltLanguageAccept()
    {
        return $this->altLanguageAccept;
    }

    /**
     * Sets Accept Text in Alternative Language.
     *
     * @param string $altLanguageAccept
     *   Accept Text in Alternative Language.
     *
     * @return string
     *   Accept Text in Alternative Language.
     */
    public function setAltLanguageAccept($altLanguageAccept)
    {
        $this->altLanguageAccept = $altLanguageAccept;
        return $this->altLanguageAccept;
    }

    /**
     * Returns Settings Text in Alternative Language.
     *
     * @return string
     *   Settings Text in Alternative Language.
     */
    public function getAltLanguageSettings()
    {
        return $this->altLanguageSettings;
    }

    /**
     * Sets Settings Text in Alternative Language.
     *
     * @param string $altLanguageSettings
     *   Settings Text in Alternative Language.
     *
     * @return string
     *   Settings Text in Alternative Language.
     */
    public function setAltLanguageSettings($altLanguageSettings)
    {
        $this->altLanguageSettings = $altLanguageSettings;
        return $this->altLanguageSettings;
    }

    /**
     * Returns Accept Recommended Settings Text in Alternative Language.
     *
     * @return string
     *   Accept Recommended Settings  Text in Alternative Language.
     */
    public function getAltLanguageAcceptRecommended()
    {
        return $this->altLanguageAcceptRecommended;
    }

    /**
     * Sets Accept Recommended Settings Text in Alternative Language.
     *
     * @param string $altLanguageAcceptRecommended
     *   Accept Recommended Settings Text in Alternative Language.
     *
     * @return string
     *   Accept Recommended Settings Text in Alternative Language.
     */
    public function setAltLanguageAcceptRecommended($altLanguageAcceptRecommended)
    {
        $this->altLanguageAcceptRecommended = $altLanguageAcceptRecommended;
        return $this->altLanguageAcceptRecommended;
    }

    public $altLanguageReject;

    public function getAltLanguageReject()
    {
        return $this->altLanguageReject;
    }

    public function setAltLanguageReject($altLanguageReject)
    {
        $this->altLanguageReject = $altLanguageReject;
        return $this->altLanguageReject;
    }

    public $altLanguageRejectSettings;

    public function getAltLanguageRejectSettings()
    {
        return $this->altLanguageRejectSettings;
    }

    public function setAltLanguageRejectSettings($altLanguageRejectSettings)
    {
        $this->altLanguageRejectSettings = $altLanguageRejectSettings;
        return $this->altLanguageRejectSettings;
    }

    public $altLanguageCloseLabel;

    public function getAltLanguageCloseLabel()
    {
        $this->altLanguageCloseLabel;
    }

    public function setAltLanguageCloseLabel($altLanguageCloseLabel)
    {
        $this->altLanguageCloseLabel = $altLanguageCloseLabel;
        return $this->altLanguageCloseLabel;
    }

    public $altLanguageAccessibilityAlert;

    public function getAltLanguageAccessibilityAlert()
    {
        return $this->altLanguageAccessibilityAlert;
    }

    public function setAltLanguageAccessibility($altLanguageAccessibilityAlert)
    {
        $this->altLanguageAccessibilityAlert = $altLanguageAccessibilityAlert;
        return $this->altLanguageAccessibilityAlert;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabLabelText()
    {
        return $this->altLanguageIabLabelText;
    }

    /**
     * @param mixed $altLanguageIabLabelText
     */
    public function setAltLanguageIabLabelText($altLanguageIabLabelText)
    {
        $this->altLanguageIabLabelText = $altLanguageIabLabelText;
        return $this->altLanguageIabLabelText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabDescriptionText()
    {
        return $this->altLanguageIabDescriptionText;
    }

    /**
     * @param mixed $altLanguageIabDescriptionText
     */
    public function setAltLanguageIabDescriptionText($altLanguageIabDescriptionText)
    {
        $this->altLanguageIabDescriptionText = $altLanguageIabDescriptionText;
        return $this->altLanguageIabDescriptionText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabConfigureText()
    {
        return $this->altLanguageIabConfigureText;
    }

    /**
     * @param mixed $altLanguageIabConfigureText
     */
    public function setAltLanguageIabConfigureText($altLanguageIabConfigureText)
    {
        $this->altLanguageIabConfigureText = $altLanguageIabConfigureText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabPanelTitleText()
    {
        return $this->altLanguageIabPanelTitleText;
    }

    /**
     * @param mixed $altLanguageIabPanelTitleText
     */
    public function setAltLanguageIabPanelTitleText($altLanguageIabPanelTitleText)
    {
        $this->altLanguageIabPanelTitleText = $altLanguageIabPanelTitleText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabPanelIntroText()
    {
        return $this->altLanguageIabPanelIntroText;
    }

    /**
     * @param mixed $altLanguageIabPanelIntroText
     */
    public function setAltLanguageIabPanelIntroText($altLanguageIabPanelIntroText)
    {
        $this->altLanguageIabPanelIntroText = $altLanguageIabPanelIntroText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabAboutIabText()
    {
        return $this->altLanguageIabAboutIabText;
    }

    /**
     * @param mixed $altLanguageIabAboutIabText
     */
    public function setAltLanguageIabAboutIabText($altLanguageIabAboutIabText)
    {
        $this->altLanguageIabAboutIabText = $altLanguageIabAboutIabText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabIabNameText()
    {
        return $this->altLanguageIabIabNameText;
    }

    /**
     * @param mixed $altLanguageIabIabNameText
     */
    public function setAltLanguageIabIabNameText($altLanguageIabIabNameText)
    {
        $this->altLanguageIabIabNameText = $altLanguageIabIabNameText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabIabLinkText()
    {
        return $this->altLanguageIabIabLinkText;
    }

    /**
     * @param mixed $altLanguageIabIabLinkText
     */
    public function setAltLanguageIabIabLinkText($altLanguageIabIabLinkText)
    {
        $this->altLanguageIabIabLinkText = $altLanguageIabIabLinkText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabPanelBackText()
    {
        return $this->altLanguageIabPanelBackText;
    }

    /**
     * @param mixed $altLanguageIabPanelBackText
     */
    public function setAltLanguageIabPanelBackText($altLanguageIabPanelBackText)
    {
        $this->altLanguageIabPanelBackText = $altLanguageIabPanelBackText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabVendorTitleText()
    {
        return $this->altLanguageIabVendorTitleText;
    }

    /**
     * @param mixed $altLanguageIabVendorTitleText
     */
    public function setAltLanguageIabVendorTitleText($altLanguageIabVendorTitleText)
    {
        $this->altLanguageIabVendorTitleText = $altLanguageIabVendorTitleText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabVendorConfigureText()
    {
        return $this->altLanguageIabVendorConfigureText;
    }

    /**
     * @param mixed $altLanguageIabVendorConfigureText
     */
    public function setAltLanguageIabVendorConfigureText($altLanguageIabVendorConfigureText)
    {
        $this->altLanguageIabVendorConfigureText = $altLanguageIabVendorConfigureText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabVendorBackText()
    {
        return $this->altLanguageIabVendorBackText;
    }

    /**
     * @param mixed $altLanguageIabVendorBackText
     */
    public function setAltLanguageIabVendorBackText($altLanguageIabVendorBackText)
    {
        $this->altLanguageIabVendorBackText = $altLanguageIabVendorBackText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabAcceptAllText()
    {
        return $this->altLanguageIabAcceptAllText;
    }

    /**
     * @param mixed $altLanguageIabAcceptAllText
     */
    public function setAltLanguageIabAcceptAllText($altLanguageIabAcceptAllText)
    {
        $this->altLanguageIabAcceptAllText = $altLanguageIabAcceptAllText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabRejectAllText()
    {
        return $this->altLanguageIabRejectAllText;
    }

    /**
     * @param mixed $altLanguageIabRejectAllText
     */
    public function setAltLanguageIabRejectAllText($altLanguageIabRejectAllText)
    {
        $this->altLanguageIabRejectAllText = $altLanguageIabRejectAllText;
    }

    /**
     * @return mixed
     */
    public function getAltLanguageIabBackText()
    {
        return $this->altLanguageIabBackText;
    }

    /**
     * @param mixed $altLanguageIabBackText
     */
    public function setAltLanguageIabBackText($altLanguageIabBackText)
    {
        $this->altLanguageIabBackText = $altLanguageIabBackText;
    }


}
