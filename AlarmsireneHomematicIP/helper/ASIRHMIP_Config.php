<?php

/**
 * @project       _Alarmsirene/AlarmsireneHomematicIP
 * @file          ASIRHMIP_Config.php
 * @author        Ulrich Bittner
 * @copyright     2022 Ulrich Bittner
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

/** @noinspection PhpUnused */

declare(strict_types=1);

trait ASIRHMIP_Config
{
    /**
     * Reloads the configuration form.
     *
     * @return void
     */
    public function ReloadConfig(): void
    {
        $this->ReloadForm();
    }

    /**
     * Modifies a configuration button.
     *
     * @param string $Field
     * @param string $Caption
     * @param int $ObjectID
     * @return void
     */
    public function ModifyButton(string $Field, string $Caption, int $ObjectID): void
    {
        $state = false;
        if ($ObjectID > 1 && @IPS_ObjectExists($ObjectID)) { //0 = main category, 1 = none
            $state = true;
        }
        $this->UpdateFormField($Field, 'caption', $Caption);
        $this->UpdateFormField($Field, 'visible', $state);
        $this->UpdateFormField($Field, 'objectID', $ObjectID);
    }

    /**
     * Modifies a trigger list configuration button
     *
     * @param string $Field
     * @param string $Condition
     * @return void
     */
    public function ModifyTriggerListButton(string $Field, string $Condition): void
    {
        $id = 0;
        $state = false;
        //Get variable id
        $primaryCondition = json_decode($Condition, true);
        if (array_key_exists(0, $primaryCondition)) {
            if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
                    $state = true;
                }
            }
        }
        $this->UpdateFormField($Field, 'caption', 'ID ' . $id . ' Bearbeiten');
        $this->UpdateFormField($Field, 'visible', $state);
        $this->UpdateFormField($Field, 'objectID', $id);
    }

    /**
     * Gets the configuration form.
     *
     * @return false|string
     * @throws Exception
     */
    public function GetConfigurationForm()
    {
        $form = [];

        ########## Elements

        //Info
        $form['elements'][0] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Info',
            'items'   => [
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleID',
                    'caption' => "ID:\t\t\t" . $this->InstanceID
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleDesignation',
                    'caption' => "Modul:\t\t" . self::MODULE_NAME
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModulePrefix',
                    'caption' => "Präfix:\t\t" . self::MODULE_PREFIX
                ],
                [
                    'type'    => 'Label',
                    'name'    => 'ModuleVersion',
                    'caption' => "Version:\t\t" . self::MODULE_VERSION
                ],
                [
                    'type'    => 'Label',
                    'caption' => ' '
                ],
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'Note',
                    'caption' => 'Notiz',
                    'width'   => '600px'
                ]
            ]
        ];

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Funktionen',
            'items'   => [
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableActive',
                    'caption' => 'Aktiv (Schalter im WebFront)'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAlarmSiren',
                    'caption' => 'Alarmsirene'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableAcousticSignal',
                    'caption' => 'Akustisches Signal'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableOpticalSignal',
                    'caption' => 'Optisches Signal'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableDurationUnit',
                    'caption' => 'Einheit Zeitdauer'
                ],
                [
                    'type'    => 'CheckBox',
                    'name'    => 'EnableDurationValue',
                    'caption' => 'Wert Zeitdauer'
                ]
            ]
        ];

        //Alarm siren
        $deviceInstance = $this->ReadPropertyInteger('DeviceInstance');
        $enableDeviceInstanceButton = false;
        if ($deviceInstance > 1 && @IPS_ObjectExists($deviceInstance)) { //0 = main category, 1 = none
            $enableDeviceInstanceButton = true;
        }

        //Acoustic state
        $deviceStateAcousticAlarm = $this->ReadPropertyInteger('DeviceStateAcousticAlarm');
        $enableDeviceStateAcousticAlarmButton = false;
        if ($deviceStateAcousticAlarm > 1 && @IPS_ObjectExists($deviceStateAcousticAlarm)) { //0 = main category, 1 = none
            $enableDeviceStateAcousticAlarmButton = true;
        }

        //Optical state
        $deviceStateOpticalAlarm = $this->ReadPropertyInteger('DeviceStateOpticalAlarm');
        $enableDeviceStateOpticalAlarmButton = false;
        if ($deviceStateOpticalAlarm > 1 && @IPS_ObjectExists($deviceStateOpticalAlarm)) { //0 = main category, 1 = none
            $enableDeviceStateOpticalAlarmButton = true;
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Alarmsirene',
            'items'   => [
                [
                    'type'    => 'Select',
                    'name'    => 'DeviceType',
                    'caption' => 'Typ',
                    'options' => [
                        [
                            'caption' => 'Kein Gerät',
                            'value'   => 0
                        ],
                        [
                            'caption' => 'HmIP-ASIR, Kanal 3',
                            'value'   => 1
                        ],
                        [
                            'caption' => 'HmIP-ASIR-2, Kanal 3',
                            'value'   => 2
                        ],
                        [
                            'caption' => 'HmIP-ASIR-O, Kanal 3',
                            'value'   => 3
                        ]
                    ]
                ],
                [
                    'type'  => 'RowLayout',
                    'items' => [
                        [
                            'type'     => 'SelectInstance',
                            'name'     => 'DeviceInstance',
                            'caption'  => 'Instanz',
                            'width'    => '600px',
                            'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "DeviceInstanceConfigurationButton", "ID " . $DeviceInstance . " Instanzkonfiguration", $DeviceInstance);'
                        ],
                        [
                            'type'    => 'Label',
                            'caption' => ' '
                        ],
                        [
                            'type'     => 'OpenObjectButton',
                            'name'     => 'DeviceInstanceConfigurationButton',
                            'caption'  => 'ID ' . $deviceInstance . ' Instanzkonfiguration',
                            'visible'  => $enableDeviceInstanceButton,
                            'objectID' => $deviceInstance
                        ]
                    ]
                ],
                [
                    'type'  => 'RowLayout',
                    'items' => [
                        [
                            'type'     => 'SelectVariable',
                            'name'     => 'DeviceStateAcousticAlarm',
                            'caption'  => 'Variable ACOUSTIC_ALARM_ACTIVE (Akustischer Alarm)',
                            'width'    => '600px',
                            'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "DeviceStateAcousticAlarmConfigurationButton", "ID " . $DeviceStateAcousticAlarm . " bearbeiten", $DeviceStateAcousticAlarm);'
                        ],
                        [
                            'type'    => 'Label',
                            'caption' => ' '
                        ],
                        [
                            'type'     => 'OpenObjectButton',
                            'name'     => 'DeviceStateAcousticAlarmConfigurationButton',
                            'caption'  => 'ID ' . $deviceStateAcousticAlarm . ' bearbeiten',
                            'visible'  => $enableDeviceStateAcousticAlarmButton,
                            'objectID' => $deviceStateAcousticAlarm
                        ]
                    ]
                ],
                [
                    'type'  => 'RowLayout',
                    'items' => [
                        [
                            'type'     => 'SelectVariable',
                            'name'     => 'DeviceStateOpticalAlarm',
                            'caption'  => 'Variable OPTICAL_ALARM_ACTIVE (Optischer Alarm)',
                            'width'    => '600px',
                            'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "DeviceStateOpticalAlarmConfigurationButton", "ID " . $DeviceStateOpticalAlarm . " aufrufen", $DeviceStateOpticalAlarm);'
                        ],
                        [
                            'type'    => 'Label',
                            'caption' => ' '
                        ],
                        [
                            'type'     => 'OpenObjectButton',
                            'name'     => 'DeviceStateOpticalAlarmConfigurationButton',
                            'caption'  => 'ID ' . $deviceStateOpticalAlarm . ' bearbeiten',
                            'visible'  => $enableDeviceStateOpticalAlarmButton,
                            'objectID' => $deviceStateOpticalAlarm
                        ]
                    ]
                ],
                [
                    'type'    => 'NumberSpinner',
                    'name'    => 'SwitchingDelay',
                    'caption' => 'Schaltverzögerung',
                    'minimum' => 0,
                    'suffix'  => 'Millisekunden'
                ]
            ]
        ];

        //Trigger list
        $triggerListValues = [];
        $variables = json_decode($this->ReadPropertyString('TriggerList'), true);
        foreach ($variables as $variable) {
            $rowColor = '#C0FFC0'; //light green
            if (!$variable['Use']) {
                $rowColor = '#DFDFDF'; //grey
            }
            //Primary condition
            if ($variable['PrimaryCondition'] != '') {
                $primaryCondition = json_decode($variable['PrimaryCondition'], true);
                if (array_key_exists(0, $primaryCondition)) {
                    if (array_key_exists(0, $primaryCondition[0]['rules']['variable'])) {
                        $id = $primaryCondition[0]['rules']['variable'][0]['variableID'];
                        if ($id <= 1 || !@IPS_ObjectExists($id)) { //0 = main category, 1 = none
                            $rowColor = '#FFC0C0'; //red
                        }
                    }
                }
            }
            //Secondary condition, multi
            if ($variable['SecondaryCondition'] != '') {
                $secondaryConditions = json_decode($variable['SecondaryCondition'], true);
                if (array_key_exists(0, $secondaryConditions)) {
                    if (array_key_exists('rules', $secondaryConditions[0])) {
                        $rules = $secondaryConditions[0]['rules']['variable'];
                        foreach ($rules as $rule) {
                            if (array_key_exists('variableID', $rule)) {
                                $id = $rule['variableID'];
                                if ($id <= 1 || !@IPS_ObjectExists($id)) { //0 = main category, 1 = none
                                    $rowColor = '#FFC0C0'; //red
                                }
                            }
                        }
                    }
                }
            }
            $triggerListValues[] = ['rowColor' => $rowColor];
        }

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Auslöser',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'TriggerList',
                    'rowCount' => 15,
                    'add'      => true,
                    'delete'   => true,
                    'columns'  => [
                        [
                            'caption' => 'Aktiviert',
                            'name'    => 'Use',
                            'width'   => '100px',
                            'add'     => true,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'caption' => 'Bezeichnung',
                            'name'    => 'Designation',
                            'onClick' => self::MODULE_PREFIX . '_ModifyTriggerListButton($id, "TriggerListConfigurationButton", $TriggerList["PrimaryCondition"]);',
                            'width'   => '400px',
                            'add'     => '',
                            'edit'    => [
                                'type' => 'ValidationTextBox'
                            ]
                        ],
                        [
                            'caption' => ' ',
                            'name'    => 'SpacerPrimaryCondition',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type' => 'Label'
                            ]
                        ],
                        [
                            'caption' => 'Bedingung:',
                            'name'    => 'LabelPrimaryCondition',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type'   => 'Label',
                                'italic' => true,
                                'bold'   => true
                            ]
                        ],
                        [
                            'caption' => 'Mehrfachauslösung',
                            'name'    => 'UseMultipleAlerts',
                            'width'   => '200px',
                            'add'     => false,
                            'edit'    => [
                                'type' => 'CheckBox'
                            ]
                        ],
                        [
                            'caption' => ' ',
                            'name'    => 'PrimaryCondition',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type' => 'SelectCondition'
                            ]
                        ],
                        [
                            'caption' => ' ',
                            'name'    => 'SpacerSecondaryCondition',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type' => 'Label'
                            ]
                        ],
                        [
                            'caption' => 'Weitere Bedingung(en):',
                            'name'    => 'LabelSecondaryCondition',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type'   => 'Label',
                                'italic' => true,
                                'bold'   => true
                            ]
                        ],
                        [
                            'caption' => ' ',
                            'name'    => 'SecondaryCondition',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type'  => 'SelectCondition',
                                'multi' => true
                            ]
                        ],
                        [
                            'caption' => ' ',
                            'name'    => 'SpacerSignaling',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type' => 'Label'
                            ]
                        ],
                        [
                            'caption' => 'Signalisierung:',
                            'name'    => 'LabelSignaling',
                            'width'   => '200px',
                            'add'     => '',
                            'visible' => false,
                            'edit'    => [
                                'type'   => 'Label',
                                'italic' => true,
                                'bold'   => true
                            ]
                        ],
                        [
                            'caption' => 'Akustisches Signal',
                            'name'    => 'AcousticSignal',
                            'width'   => '300px',
                            'add'     => 0,
                            'visible' => false,
                            'edit'    => [
                                'type'    => 'Select',
                                'options' => [
                                    [
                                        'caption' => '0 - Kein akustisches Signal',
                                        'value'   => 0
                                    ],
                                    [
                                        'caption' => '1 - Frequenz steigend',
                                        'value'   => 1
                                    ],
                                    [
                                        'caption' => '2 - Frequenz fallend',
                                        'value'   => 2
                                    ],
                                    [
                                        'caption' => '3 - Frequenz steigend/fallend',
                                        'value'   => 3
                                    ],
                                    [
                                        'caption' => '4 - Frequenz tief/hoch',
                                        'value'   => 4
                                    ],
                                    [
                                        'caption' => '5 - Frequenz tief/mittel/hoch',
                                        'value'   => 5
                                    ],
                                    [
                                        'caption' => '6 - Frequenz hoch ein/aus',
                                        'value'   => 6
                                    ],
                                    [
                                        'caption' => '7 - Frequenz hoch ein, lang aus',
                                        'value'   => 7
                                    ],
                                    [
                                        'caption' => '8 - Frequenz tief ein/aus, hoch ein/aus',
                                        'value'   => 8
                                    ],
                                    [
                                        'caption' => '9 - Frequenz tief ein - lang aus, hoch ein - lang aus',
                                        'value'   => 9
                                    ],
                                    [
                                        'caption' => '10 - Batterie leer',
                                        'value'   => 10
                                    ],
                                    [
                                        'caption' => '11 - Unscharf',
                                        'value'   => 11
                                    ],
                                    [
                                        'caption' => '12 - Intern scharf',
                                        'value'   => 12
                                    ],
                                    [
                                        'caption' => '13 - Extern scharf',
                                        'value'   => 13
                                    ],
                                    [
                                        'caption' => '14 - Verzögert intern scharf',
                                        'value'   => 14
                                    ],
                                    [
                                        'caption' => '15 - Verzögert extern scharf',
                                        'value'   => 15
                                    ],
                                    [
                                        'caption' => '16 - Alarm Ereignis',
                                        'value'   => 16
                                    ],
                                    [
                                        'caption' => '17 - Fehler',
                                        'value'   => 17
                                    ]
                                ]
                            ]
                        ],
                        [
                            'caption' => 'Optisches Signal',
                            'name'    => 'OpticalSignal',
                            'width'   => '300px',
                            'add'     => 0,
                            'visible' => false,
                            'edit'    => [
                                'type'    => 'Select',
                                'options' => [
                                    [
                                        'caption' => '0 - Kein optisches Signal',
                                        'value'   => 0
                                    ],
                                    [
                                        'caption' => '1 - Abwechselndes langsames Blinken',
                                        'value'   => 1
                                    ],
                                    [
                                        'caption' => '2 - Gleichzeitiges langsames Blinken',
                                        'value'   => 2
                                    ],
                                    [
                                        'caption' => '3 - Gleichzeitiges schnelles Blinken',
                                        'value'   => 3
                                    ],
                                    [
                                        'caption' => '4 - Gleichzeitiges kurzes Blinken',
                                        'value'   => 4
                                    ],
                                    [
                                        'caption' => '5 - Bestätigungssignal 0 - lang lang',
                                        'value'   => 5
                                    ],
                                    [
                                        'caption' => '6 - Bestätigungssignal 1 - lang kurz',
                                        'value'   => 6
                                    ],
                                    [
                                        'caption' => '7 - Bestätigungssignal 2 - lang kurz kurz',
                                        'value'   => 7
                                    ]
                                ]
                            ]
                        ],
                        [
                            'caption' => 'Einheit Zeitdauer',
                            'name'    => 'DurationUnit',
                            'width'   => '200px',
                            'add'     => 0,
                            'visible' => false,
                            'edit'    => [
                                'type'    => 'Select',
                                'options' => [
                                    [
                                        'caption' => '0 - Sekunden',
                                        'value'   => 0
                                    ],
                                    [
                                        'caption' => '1 - Minuten',
                                        'value'   => 1
                                    ],
                                    [
                                        'caption' => '2 - Stunden',
                                        'value'   => 2
                                    ]
                                ]
                            ]
                        ],
                        [
                            'caption' => 'Wert Zeitdauer',
                            'name'    => 'DurationValue',
                            'width'   => '200px',
                            'add'     => 5,
                            'visible' => false,
                            'edit'    => [
                                'type'    => 'NumberSpinner',
                                'minimum' => 0
                            ]
                        ]
                    ],
                    'values' => $triggerListValues,
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'TriggerListConfigurationButton',
                    'caption'  => 'Bearbeiten',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Command control
        $id = $this->ReadPropertyInteger('CommandControl');
        $enableButton = false;
        if ($id > 1 && @IPS_ObjectExists($id)) { //0 = main category, 1 = none
            $enableButton = true;
        }
        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Ablaufsteuerung',
            'items'   => [
                [
                    'type'  => 'RowLayout',
                    'items' => [
                        [
                            'type'     => 'SelectModule',
                            'name'     => 'CommandControl',
                            'caption'  => 'Instanz',
                            'moduleID' => self::ABLAUFSTEUERUNG_MODULE_GUID,
                            'width'    => '600px',
                            'onChange' => self::MODULE_PREFIX . '_ModifyButton($id, "CommandControlConfigurationButton", "ID " . $CommandControl . " Instanzkonfiguration", $CommandControl);'
                        ],
                        [
                            'type'    => 'Button',
                            'caption' => 'Neue Instanz erstellen',
                            'onClick' => self::MODULE_PREFIX . '_CreateCommandControlInstance($id);'
                        ],
                        [
                            'type'    => 'Label',
                            'caption' => ' '
                        ],
                        [
                            'type'     => 'OpenObjectButton',
                            'caption'  => 'ID ' . $id . ' Instanzkonfiguration',
                            'name'     => 'CommandControlConfigurationButton',
                            'visible'  => $enableButton,
                            'objectID' => $id
                        ]
                    ]
                ]
            ]
        ];

        $form['elements'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Deaktivierung',
            'items'   => [
                [
                    'type'    => 'CheckBox',
                    'name'    => 'UseAutomaticDeactivation',
                    'caption' => 'Automatische Deaktivierung'
                ],
                [
                    'type'    => 'SelectTime',
                    'name'    => 'AutomaticDeactivationStartTime',
                    'caption' => 'Startzeit'
                ],
                [
                    'type'    => 'SelectTime',
                    'name'    => 'AutomaticDeactivationEndTime',
                    'caption' => 'Endzeit'
                ]
            ]
        ];

        ########## Actions

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Konfiguration',
            'items'   => [
                [
                    'type'    => 'Button',
                    'caption' => 'Neu laden',
                    'onClick' => self::MODULE_PREFIX . '_ReloadConfig($id);'
                ]
            ]
        ];

        //Test center
        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Schaltfunktionen',
            'items'   => [
                [
                    'type' => 'TestCenter',
                ]
            ]
        ];

        //Registered references
        $registeredReferences = [];
        $references = $this->GetReferenceList();
        foreach ($references as $reference) {
            $name = 'Objekt #' . $reference . ' existiert nicht';
            $rowColor = '#FFC0C0'; //red
            if (@IPS_ObjectExists($reference)) {
                $name = IPS_GetName($reference);
                $rowColor = '#C0FFC0'; //light green
            }
            $registeredReferences[] = [
                'ObjectID' => $reference,
                'Name'     => $name,
                'rowColor' => $rowColor];
        }

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Registrierte Referenzen',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'RegisteredReferences',
                    'rowCount' => 10,
                    'sort'     => [
                        'column'    => 'ObjectID',
                        'direction' => 'ascending'
                    ],
                    'columns' => [
                        [
                            'caption' => 'ID',
                            'name'    => 'ObjectID',
                            'width'   => '150px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredReferencesConfigurationButton", "ID " . $RegisteredReferences["ObjectID"] . " aufrufen", $RegisteredReferences["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Name',
                            'name'    => 'Name',
                            'width'   => '300px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredReferencesConfigurationButton", "ID " . $RegisteredReferences["ObjectID"] . " aufrufen", $RegisteredReferences["ObjectID"]);'
                        ]
                    ],
                    'values' => $registeredReferences
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'RegisteredReferencesConfigurationButton',
                    'caption'  => 'Aufrufen',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        //Registered messages
        $registeredMessages = [];
        $messages = $this->GetMessageList();
        foreach ($messages as $id => $messageID) {
            $name = 'Objekt #' . $id . ' existiert nicht';
            $rowColor = '#FFC0C0'; //red
            if (@IPS_ObjectExists($id)) {
                $name = IPS_GetName($id);
                $rowColor = '#C0FFC0'; //light green
            }
            switch ($messageID) {
                case [10001]:
                    $messageDescription = 'IPS_KERNELSTARTED';
                    break;

                case [10603]:
                    $messageDescription = 'VM_UPDATE';
                    break;

                default:
                    $messageDescription = 'keine Bezeichnung';
            }
            $registeredMessages[] = [
                'ObjectID'           => $id,
                'Name'               => $name,
                'MessageID'          => $messageID,
                'MessageDescription' => $messageDescription,
                'rowColor'           => $rowColor];
        }

        $form['actions'][] = [
            'type'    => 'ExpansionPanel',
            'caption' => 'Registrierte Nachrichten',
            'items'   => [
                [
                    'type'     => 'List',
                    'name'     => 'RegisteredMessages',
                    'rowCount' => 10,
                    'sort'     => [
                        'column'    => 'ObjectID',
                        'direction' => 'ascending'
                    ],
                    'columns' => [
                        [
                            'caption' => 'ID',
                            'name'    => 'ObjectID',
                            'width'   => '150px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredMessagesConfigurationButton", "ID " . $RegisteredMessages["ObjectID"] . " aufrufen", $RegisteredMessages["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Name',
                            'name'    => 'Name',
                            'width'   => '300px',
                            'onClick' => self::MODULE_PREFIX . '_ModifyButton($id, "RegisteredMessagesConfigurationButton", "ID " . $RegisteredMessages["ObjectID"] . " aufrufen", $RegisteredMessages["ObjectID"]);'
                        ],
                        [
                            'caption' => 'Nachrichten ID',
                            'name'    => 'MessageID',
                            'width'   => '150px'
                        ],
                        [
                            'caption' => 'Nachrichten Bezeichnung',
                            'name'    => 'MessageDescription',
                            'width'   => '250px'
                        ]
                    ],
                    'values' => $registeredMessages
                ],
                [
                    'type'     => 'OpenObjectButton',
                    'name'     => 'RegisteredMessagesConfigurationButton',
                    'caption'  => 'Aufrufen',
                    'visible'  => false,
                    'objectID' => 0
                ]
            ]
        ];

        ########## Status

        $form['status'][] = [
            'code'    => 101,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' wird erstellt',
        ];
        $form['status'][] = [
            'code'    => 102,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' ist aktiv',
        ];
        $form['status'][] = [
            'code'    => 103,
            'icon'    => 'active',
            'caption' => self::MODULE_NAME . ' wird gelöscht',
        ];
        $form['status'][] = [
            'code'    => 104,
            'icon'    => 'inactive',
            'caption' => self::MODULE_NAME . ' ist inaktiv',
        ];
        $form['status'][] = [
            'code'    => 200,
            'icon'    => 'inactive',
            'caption' => 'Es ist Fehler aufgetreten, weitere Informationen unter Meldungen, im Log oder Debug!',
        ];

        return json_encode($form);
    }
}