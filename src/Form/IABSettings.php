<?php

namespace Drupal\CivicCookiecontrol\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\dblog\Plugin\views\wizard\Watchdog;
use Drupal\node\Entity\Node;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * The configuration form for cookie control settings.
 */
class IABSettings extends ConfigFormBase
{

    protected $countryManager;

    protected $itemsCount;

    protected $config;

    /**
     * Constructor.
     */
    public function __construct(CountryManager $countryManager)
    {
        $this->countryManager = $countryManager;
        $this->config = \Drupal::configFactory()
            ->getEditable('iab.settings');

        _check_cookie_categories();

    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        // Instantiates this form class.
        return new static(
        // Load the service required to construct this class.
            $container->get('country_manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'iab_config_form';
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $configData = $this->config->get();
        $recommendedStateArray=[];
        foreach ($configData as $key => $configValue) {
            if ($key == 'iabRecommendedState') {
                for($i=1;$i<=5;$i++){
                    if ($form_state->getValue('iabRecommendedState_' . $i)) {
                        $recommendedStateArray[$i] = $form_state->getValue('iabRecommendedState_' . $i) ? TRUE : FALSE ;
                    }
                }
                if (sizeof($recommendedStateArray)>0) {
                    $this->config->set($key, Json::encode($recommendedStateArray))->save();
                }
            }else if (strpos($key, 'iab') !== FALSE) {
                if (strpos($key, 'Text') !== FALSE) {
                    if ($form_state->getValue($key) != '') {
                        $this->config->set($key, str_replace([
                            "\r\n",
                            "\n",
                            "\r",
                        ], '', $form_state->getValue($key)))->save();
                    }
                } else {
                    $this->config->set($key, $form_state->getValue($key))->save();
                }
            }
        }
        \Drupal::cache()->delete('civiccookiecontrol_config');
        parent::submitForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['iab.settings'];
    }


    public function saveIABOption(array $form, FormStateInterface $form_state){
        $this->config->set('iabCMP', $form_state->getValue('iabCMP'))->save();
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $items_num = $form_state->get('items_count');
        global $base_url;

        $form['iab'] = [
            '#type' => 'details',
            '#title' => t('IAB Settings'),
            '#open' => TRUE,
        ];

        $form['iab']['iabCMP'] = [
            '#type' => 'radios',
            '#title' => t("Enable IAB Support."),
            '#options' => [
                TRUE => t("Yes"),
                FALSE => t('No'),
            ],
            '#ajax' => [
                'callback' => '::saveIABOption',
                'effect' => 'fade',
                'progress' => [
                    'type' => 'throbber',
                ],
            ],
            '#default_value' => \Drupal::config('iab.settings')
                ->get('iabCMP') ? 1 : 0,
            '#description' => t("Whether or not Cookie Control supports the IAB's TCF v1.1."),
        ];
        $form['iab']['iabGdprAppliesGlobally'] = [
            '#type' => 'radios',
            '#title' => t('Obtain consent from all users regardless of their location'),
            '#options' => [
                TRUE => t("Yes"),
                FALSE => t('No'),
            ],
            '#states' => [
                // Action to take.
                'invisible' => [
                    ':input[name=iabCMP]' => [
                        'value' => 0,
                    ],
                ],
            ],
            '#default_value' => \Drupal::config('iab.settings')
                ->get('iabGdprAppliesGlobally') ? 1 : 0,
            '#description' => t("Determines whether or not consent should be obtained from all users regardless of their location, or if we ought to only seek it from those within the EU. Please note, if you have excludedCountries set up as part of your main Cookie Control configuration, this value will dynamically change to match depending on the locale of the site visitor."),
        ];

        $form['iabRecommendedState'] = [
            '#type' => 'details',
            '#title' => t('IAB Recommended State'),
            '#open' => FALSE,
            '#states' => [
                // Action to take.
                'invisible' => [
                    ':input[name=iabCMP]' => [
                        'value' => 0,
                    ],
                ],
            ],
        ];

        $iabRecommendedStateArray = Json::decode(\Drupal::config('iab.settings')->get('iabRecommendedState'));
        $form['iabRecommendedState']['iabRecommendedState_1'] = [
            '#type' => 'radios',
            '#title' => t('Recommended State for IAB purpose of information storage and access.'),
            '#options' => [
                TRUE => t("On"),
                FALSE => t('off'),
            ],
            '#default_value' => $iabRecommendedStateArray[1]  ? 1 : 0,
            '#description' => t("Sets the default value for information storage and access IAB purpose."),
        ];
        $form['iabRecommendedState']['iabRecommendedState_2'] = [
            '#type' => 'radios',
            '#title' => t('Recommended State for IAB purpose of Personalisation.'),
            '#options' => [
                TRUE => t("On"),
                FALSE => t('off'),
            ],
            '#default_value' => $iabRecommendedStateArray[2] ? 1 : 0,
            '#description' => t("Sets the default value for Personalisation IAB purpose."),
        ];

        $form['iabRecommendedState']['iabRecommendedState_3'] = [
            '#type' => 'radios',
            '#title' => t('Recommended State for IAB purpose of Ad selection, delivery, reporting.'),
            '#options' => [
                TRUE => t("On"),
                FALSE => t('off'),
            ],
            '#default_value' => $iabRecommendedStateArray[3] ? 1 : 0,
            '#description' => t("Sets the default value for Ad selection, delivery, reporting IAB purpose."),
        ];
        $form['iabRecommendedState']['iabRecommendedState_4'] = [
            '#type' => 'radios',
            '#title' => t('Recommended State for IAB purpose of Content selection, delivery, reporting.'),
            '#options' => [
                TRUE => t("On"),
                FALSE => t('off'),
            ],
            '#default_value' => $iabRecommendedStateArray[4] ? 1 : 0,
            '#description' => t("Sets the default value for Content selection, delivery, reporting IAB purpose."),
        ];
        $form['iabRecommendedState']['iabRecommendedState_5'] = [
            '#type' => 'radios',
            '#title' => t('Recommended State for IAB purpose of Measurement.'),
            '#options' => [
                TRUE => t("On"),
                FALSE => t('off'),
            ],
            '#default_value' => $iabRecommendedStateArray[5] ? 1 : 0,
            '#description' => t("Sets the default value for Measurement IAB purpose."),
        ];

        $form['iabTexts'] = [
            '#type' => 'details',
            '#title' => t('IAB Texts'),
            '#open' => FALSE,
            '#states' => [
                // Action to take.
                'invisible' => [
                    ':input[name=iabCMP]' => [
                        'value' => 0,
                    ],
                ],
            ],
        ];

        $form['iabTexts']['iabLabelText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Label'),
            '#description' => t('Replacement text for "Ad Vendors"'),
            '#default_value' => \Drupal::config('iab.settings')
                ->get('iabLabelText'),
        ];
        $form['iabTexts']['iabDescriptionText'] = [
            '#type' => 'textarea',
            '#title' => t('IAB Description'),
            '#description' => t('Set the description text for IAB'),
            '#default_value' => \Drupal::config('iab.settings')
                ->get('iabDescriptionText'),
        ];
        $form['iabTexts']['iabConfigureText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Configure Text'),
            '#description' => t('Set the label for the IAB cofiguration button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabConfigureText'),
        ];
        $form['iabTexts']['iabPanelTitleText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Panel Title'),
            '#description' => t('Set the title for the IAB panel.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabPanelTitleText'),
        ];
        $form['iabTexts']['iabPanelIntroText'] = [
            '#type' => 'textarea',
            '#title' => t('IAB Panel Introduction Text'),
            '#description' => t('Set the introductory text for the IAB panel.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabPanelIntroText'),
        ];
        $form['iabTexts']['iabAboutIabText'] = [
            '#type' => 'textarea',
            '#title' => t('About IAB Text'),
            '#description' => t('Set the about AIB text.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabAboutIabText'),
        ];
        $form['iabTexts']['iabIabNameText'] = [
            '#type' => 'textarea',
            '#title' => t('About IAB Text'),
            '#description' => t('Set the about AIB text.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabIabNameText'),
        ];

        $form['iabTexts']['iabIabLinkText'] = [
            '#type' => 'url',
            '#title' => t('IAB Link'),
            '#description' => t('Set the URL for IAB link.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabIabLinkText'),
        ];

        $form['iabTexts']['iabPanelBackText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Panel Back Text'),
            '#description' => t('Set the text for the "Back" button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabPanelBackText'),
        ];
        $form['iabTexts']['iabVendorTitleText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Vendor Title Text'),
            '#description' => t('Set the text for  Vendor Title.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabVendorTitleText'),
        ];
        $form['iabTexts']['iabVendorConfigureText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Vendor Configure Text'),
            '#description' => t('Set the text for IAB vendors configuration button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabVendorConfigureText'),
        ];
        $form['iabTexts']['iabVendorBackText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Back to Vendor purposes title.'),
            '#description' => t('Sets label for the back to vendor purposes button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabVendorBackText'),
        ];
        $form['iabTexts']['iabAcceptAllText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Accept All Label.'),
            '#description' => t('Sets label for the "Accept All" button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabAcceptAllText'),
        ];
        $form['iabTexts']['iabRejectAllText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB Reject All Label.'),
            '#description' => t('Sets label for the "Reject All" button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabRejectAllText'),
        ];
        $form['iabTexts']['iabBackText'] = [
            '#type' => 'textfield',
            '#title' => t('IAB "Back" button text.'),
            '#description' => t('Sets label for the "Back" button.'),
            '#default_value' => \Drupal::config('iab.settings')->get('iabBackText'),
        ];


        /*$form['#attached'] = [
          'library' => [
             'civiccookiecontrol/civiccookiecontrol.admin',
             'civiccookiecontrol/civiccookiecontrol.admin_css',
             'civiccookiecontrol/civiccookiecontrol.minicolors',
             'civiccookiecontrol/civiccookiecontrol.lct'
          ],
            ];*/
        // $form['#theme'] = 'cookiecontrol-admin-form';.
        $form_state->setCached(FALSE);

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Save IAB Configuration'),
            '#button_type' => 'primary',
        ];

        return $form;

    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

}
