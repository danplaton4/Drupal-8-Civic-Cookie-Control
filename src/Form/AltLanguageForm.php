<?php

namespace Drupal\civiccookiecontrol\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to add/edit Alternative Languages.
 */
class AltLanguageForm extends EntityForm {

  private $cookieCategories;

  /**
   * CookieCategoryForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->cookieCategories = \Drupal::entityTypeManager()
      ->getStorage('cookiecategory')
      ->loadMultiple();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $altLanguage = $this->entity;

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('Edit Alternative Language: @name', ['@name' => $altLanguage->label()]);
    }
    else {
      $form['#title'] = $this->t('Add Alternative Language');
    }

      $form['ccc'] = [
          '#type' => 'details',
          '#title' => t('Cookie Control Widget Translations'),
          '#open' => TRUE,
      ];

    $form['ccc']['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $altLanguage->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$altLanguage->isNew(),
      '#access' => FALSE,
    ];

    $form['ccc']['altLanguageIsoCode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alternative Language (Iso Code)'),
      '#maxlength' => 125,
      '#default_value' => $altLanguage->label(),
      '#description' => $this->t("The Language Iso code Name."),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageTitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title in Alternative Language'),
      '#maxlength' => 255,
      '#default_value' => $altLanguage->altLanguageTitle,
      '#description' => $this->t("Title in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageIntro'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Intro in Alternative Language'),
      '#default_value' => $altLanguage->altLanguageIntro,
      '#description' => $this->t("Intro in Alternative Language"),
      '#required' => TRUE,
    ];

      $form['ccc']['altLanguageAcceptRecommended'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Accept Recommended Settings Text in Alternative Language'),
          '#maxlength' => 512,
          '#default_value' => $altLanguage->altLanguageAcceptRecommended,
          '#description' => $this->t("Accept Text in Alternative Language"),
          '#required' => TRUE,
      ];

      $form['ccc']['altLanguageRejectSettings'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Reject Settings Text in Alternative Language'),
          '#maxlength' => 512,
          '#default_value' => $altLanguage->altLanguageRejectSettings,
          '#description' => $this->t("Reject Settings Text in Alternative Language"),
          '#required' => TRUE,
      ];

    $form['ccc']['altLanguageNecessaryTitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Necessary Title in Alternative Language'),
      '#maxlength' => 255,
      '#default_value' => $altLanguage->altLanguageNecessaryTitle,
      '#description' => $this->t("Necessary Title in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageNecessaryDescription'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Necessary Description in Alternative Language'),
      '#default_value' => $altLanguage->altLanguageNecessaryDescription,
      '#description' => $this->t("Necessary Description in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageThirdPartyTitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Third Party Title in Alternative Language'),
      '#maxlength' => 512,
      '#default_value' => $altLanguage->altLanguageThirdPartyTitle,
      '#description' => $this->t("Third Party Title in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageThirdPartyDescription'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Third Party Description in Alternative Language'),
      '#default_value' => $altLanguage->altLanguageThirdPartyDescription,
      '#description' => $this->t("Third Party Description in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageOn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('On Text in Alternative Language'),
      '#maxlength' => 128,
      '#default_value' => $altLanguage->altLanguageOn,
      '#description' => $this->t("On Text in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageOff'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Off Text in Alternative Language'),
      '#maxlength' => 128,
      '#default_value' => $altLanguage->altLanguageOff,
      '#description' => $this->t("Off Text in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageNotifyTitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Notify Title Text in Alternative Language'),
      '#maxlength' => 512,
      '#default_value' => $altLanguage->altLanguageNotifyTitle,
      '#description' => $this->t("Notify Title Text in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageNotifyDescription'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Notify Description Text in Alternative Language'),
      '#default_value' => $altLanguage->altLanguageNotifyDescription,
      '#description' => $this->t("Notify Description Text in Alternative Language"),
      '#required' => TRUE,
    ];

    $form['ccc']['altLanguageAccept'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Accept Text in Alternative Language'),
      '#maxlength' => 512,
      '#default_value' => $altLanguage->altLanguageAccept,
      '#description' => $this->t("Accept Text in Alternative Language"),
      '#required' => TRUE,
    ];

  $form['ccc']['altLanguageReject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reject Text in Alternative Language'),
      '#maxlength' => 512,
      '#default_value' => $altLanguage->altLanguageReject,
      '#description' => $this->t("Reject Text in Alternative Language"),
      '#required' => TRUE,
  ];


    $form['ccc']['altLanguageSettings'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Settings Text in Alternative Language'),
      '#maxlength' => 512,
      '#default_value' => $altLanguage->altLanguageSettings,
      '#description' => $this->t("Settings Text in Alternative Language"),
      '#required' => TRUE,
    ];

      $form['ccc']['altLanguageCloseLabel'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Close Label Text in Alternative Language'),
          '#maxlength' => 512,
          '#default_value' => $altLanguage->altLanguageCloseLabel,
          '#description' => $this->t("Close Label Text in Alternative Language"),
          '#required' => TRUE,
      ];

      $form['ccc']['altLanguageAccessibilityAlert'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Accessibility Alert in Alternative Language'),
          '#maxlength' => 512,
          '#default_value' => $altLanguage->altLanguageAccessibilityAlert,
          '#description' => $this->t("Accessibility Alert in Alternative Language"),
          '#required' => TRUE,
      ];
    $optCookiesAltLang = json_decode($this->entity->getAltLanguageOptionalCookies());
    $i = 0;
    foreach ($this->cookieCategories as $cookieCat) {
      $form['ccc']['altLanguageOptionalCookiesLabel_' . $cookieCat->id()] = [
        '#type' => 'textfield',
        '#title' => ucfirst($cookieCat->getCookieName()) . " " . $this->t('Label in Alternative Language'),
        '#maxlength' => 512,
        '#default_value' => $optCookiesAltLang[$i]->label,
        '#description' => ucfirst($cookieCat->getCookieName()) . " " . $this->t("Cookie Label in Alternative Language"),
        '#required' => TRUE,
      ];

      $form['ccc']['altLanguageOptionalCookiesDescription_' . $cookieCat->id()] = [
        '#type' => 'textarea',
        '#title' => ucfirst($cookieCat->getCookieName()) . " " . $this->t('Optional Cookies Description in Alternative Language'),
        '#default_value' => $optCookiesAltLang[$i]->description,
        '#description' => ucfirst($cookieCat->getCookieName()) . " " . $this->t("Cookie Description in Alternative Language"),
        '#required' => TRUE,
      ];
      $i++;
    }

    $form['ccc']['altLanguageStmtDescrText'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Statements Description Text in Alternative Language'),
      '#default_value' => $altLanguage->altLanguageStmtDescrText,
      '#description' => $this->t("Statements Description Text in Alternative Language"),
      '#required' => FALSE,
    ];

    $form['ccc']['altLanguageStmtNameText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Statements Name Text in Alternative Language'),
      '#maxlength' => 512,
      '#default_value' => $altLanguage->altLanguageStmtNameText,
      '#description' => $this->t("Statements Name Text in Alternative Language"),
      '#required' => FALSE,
    ];

    global $base_url;
    $form['ccc']['altLanguageStmtUrl'] = [
      '#type' => 'number',
      '#title' => $this->t('Statement URL for Alternative Language'),
      '#size' => 5,
      '#min' => 1,
      '#step' => 1,
      '#field_prefix' => $base_url . '/node/',
      '#default_value' => $altLanguage->altLanguageStmtUrl,
      '#description' => $this->t("Statement URL in Alternative Language"),
      '#required' => FALSE,
    ];

    $form['ccc']['altLanguageStmtDate'] = [
      '#type' => 'date',
      '#title' => t('Statement Updated Date for Alternative Language'),
      '#format' => 'd/m/Y',
      '#date_date_format' => 'd/m/Y',
      '#default_value' => $altLanguage->altLanguageStmtDate,
      '#required' => FALSE,
    ];
      if (\Drupal::config('iab.settings')->get('iabCMP') == 1 ) {
          $form['iabTexts'] = [
              '#type' => 'details',
              '#title' => t('IAB Texts in Alternative Language'),
              '#open' => FALSE,
          ];

          $form['iabTexts']['altLanguageIabLabelText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Label in Alternative Language'),
              '#description' => t('Replacement text for "Ad Vendors" in Alternative Language'),
              '#default_value' => $altLanguage->altLanguageIabLabelText,
          ];
          $form['iabTexts']['altLanguageIabDescriptionText'] = [
              '#type' => 'textarea',
              '#title' => t('IAB Description in Alternative Language'),
              '#description' => t('Set the description text for IAB in Alternative Language'),
              '#default_value' => $altLanguage->altLanguageIabDescriptionText,
          ];
          $form['iabTexts']['altLanguageIabConfigureText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Configure Text in Alternative Language'),
              '#description' => t('Set the label for the IAB cofiguration button in Alternative Language'),
              '#default_value' => $altLanguage->altLanguageIabConfigureText,
          ];
          $form['iabTexts']['altLanguageIabPanelTitleText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Panel Title in Alternative Language'),
              '#description' => t('Set the title for the IAB panel in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabPanelTitleText,
          ];

          $form['iabTexts']['altLanguageIabPanelIntroText'] = [
              '#type' => 'textarea',
              '#title' => t('IAB Panel Introduction Text in Alternative Language'),
              '#description' => t('Set the introductory text for the IAB panel in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabPanelIntroText,
          ];
          $form['iabTexts']['altLanguageIabAboutIabText'] = [
              '#type' => 'textarea',
              '#title' => t('About IAB Text in Alternative Language'),
              '#description' => t('Set the about AIB text in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabAboutIabText,
          ];

          $form['iabTexts']['altLanguageIabIabNameText'] = [
              '#type' => 'textarea',
              '#title' => t('IAB Name Text in Alternative Language'),
              '#description' => t('Set the IAB name text in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabIabNameText,
          ];

          $form['iabTexts']['altLanguageIabIabLinkText'] = [
              '#type' => 'url',
              '#title' => t('IAB Link in Alternative Language'),
              '#description' => t('Set the URL for IAB link in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabIabLinkText,
          ];

          $form['iabTexts']['altLanguageIabPanelBackText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Panel Back Text in Alternative Language'),
              '#description' => t('Set the text for the "Back" button in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabPanelBackText,
          ];
          $form['iabTexts']['altLanguageIabVendorTitleText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Vendor Title Text in Alternative Language'),
              '#description' => t('Set the text for  Vendor Title in Alternative Language'),
              '#default_value' => $altLanguage->altLanguageIabVendorTitleText,
          ];
          $form['iabTexts']['altLanguageIabVendorConfigureText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Vendor Configure Text in Alternative Language'),
              '#description' => t('Set the text for IAB vendors configuration button in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabVendorConfigureText,
          ];
          $form['iabTexts']['altLanguageIabVendorBackText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Back to Vendor purposes title in Alternative Language'),
              '#description' => t('Sets label for the back to vendor purposes button in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabVendorBackText,
          ];
          $form['iabTexts']['altLanguageIabAcceptAllText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Accept All Label in Alternative Language'),
              '#description' => t('Sets label for the "Accept All" button in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabAcceptAllText,
          ];
          $form['iabTexts']['altLanguageIabRejectAllText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Reject All Label in Alternative Language'),
              '#description' => t('Sets label for the "Reject All" button in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabRejectAllText,
          ];
          $form['iabTexts']['altLanguageIabBackText'] = [
              '#type' => 'textfield',
              '#title' => t('IAB Back Button Label in Alternative Language'),
              '#description' => t('Sets label for the "Back" button in Alternative Language.'),
              '#default_value' => $altLanguage->altLanguageIabBackText,
          ];
      }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue(['altLanguageStmtUrl']) > 0) {
      $node = Node::load($form_state->getValue('altLanguageStmtUrl'));
      // If no node can be loaded give the user a suitable message prompt.
      if (!$node) {
        $form_state->setErrorByName('altLanguageStmtUrl', t('The specified privacy policy node id does not exist. Leave blank if you have not yet created a policy page.'));
      }/*else{
        $form_state->setValue('altLanguageStmtUrl', $node->toUrl());
      }*/
    }
    elseif (!is_int($form_state->getValue(['altLanguageStmtUrl']))) {
      $form_state->setErrorByName('altLanguageStmtUrl', t('Please provide a valid node id.'));
    }

    parent::validateForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    global $base_url;
    try {
      $altOptionalCookies = [];
      foreach ($this->cookieCategories as $cookieCat) {
        $altOptionalCookie = [];
        $altOptionalCookie['label'] = $form_state->getValue('altLanguageOptionalCookiesLabel_' . $cookieCat->id());
        $altOptionalCookie['description'] = $form_state->getValue('altLanguageOptionalCookiesDescription_' . $cookieCat->id());
        $altOptionalCookies[] = $altOptionalCookie;
      }

      $altLanguage = $this->entity;
      $machineName = 'alt_language_' . $form_state->getValue('altLanguageIsoCode');
      if ($altLanguage->isNew()) {
        $altLanguage->id = $machineName;
      }
      $altLanguage->setAltLanguageOptionalCookies(json_encode($altOptionalCookies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
      $status = $altLanguage->save();
      if ($status) {
          \Drupal::messenger()->addMessage(
          $this->t(
            'Saved the %label Alternative Language.', [
              '%label' => $altLanguage->label(),
            ]
          )
        );
      }
      else {
          \Drupal::messenger()->addMessage(
          $this->t(
            'The %label Alternative Language was not saved.', [
              '%label' => $altLanguage->label(),
            ]
          )
        );
      }
      \Drupal::cache()->delete('civiccookiecontrol_config');
      $form_state->setRedirect('entity.altlanguage.collection');
    } catch (EntityStorageException $ex) {
        \Drupal::messenger()->addMessage(
        $this->t(
          'The %label  Alternative Language already exist.', [
            '%label' => $altLanguage->label(),
          ]
        )
      );

      $form_state->setRedirect('entity.altlanguage.collection');
    }

  }

  /**
   * Check whether an Alternative configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('altlanguage')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
