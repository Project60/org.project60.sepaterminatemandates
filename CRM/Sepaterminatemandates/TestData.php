<?php
/**
 * Copyright (C) 2023  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

class CRM_Sepaterminatemandates_TestData {

  const STATUS_COMPLETE = 1;

  const STATUS_CANCELLED = 3;

  const STATUS_FAILED = 4;

  protected $testData = [
    1 => [
      'description' => 'Only success full contributions',
      'campaign' => 'Test Campaign',
      'amount' => 10,
      'iban' => 'NL84ARBN0847296849',
      'mandate_status' => 'RCUR',
      'financial_type_id' => 1,
      'contributions' => [
        1 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        2 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        3 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        4 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        5 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        6 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        7 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        8 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        9 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        10 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        11 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        12 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        13 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        14 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
      ],
    ],
    2 => [
      'description' => 'Success full contributions and three failure in a row',
      'campaign' => 'Test Campaign',
      'amount' => 10,
      'iban' => 'NL84ARBN0847296849',
      'mandate_status' => 'RCUR',
      'financial_type_id' => 1,
      'contributions' => [
        1 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'Test reason',
        ],
        2 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'Test reason',
        ],
        3 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'Test reason',
        ],
        4 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        5 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        6 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        7 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        8 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        9 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        10 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        11 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        12 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        13 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        14 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
      ],
    ],
    3 => [
      'description' => 'Success full contributions and three failure in a row but latest completed again',
      'campaign' => 'Test Campaign',
      'amount' => 10,
      'iban' => 'NL84ARBN0847296849',
      'mandate_status' => 'RCUR',
      'financial_type_id' => 1,
      'contributions' => [
        1 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        2 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'Test reason',
        ],
        3 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'Test reason',
        ],
        4 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'Test reason',
        ],
        5 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        6 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        7 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        8 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        9 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        10 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        11 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        12 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        13 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        14 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
      ],
    ],
    4 => [
      'description' => 'Success full contributions and three failure in a row but the latest failure more than 12 months ago',
      'campaign' => 'Test Campaign',
      'amount' => 10,
      'iban' => 'NL84ARBN0847296849',
      'mandate_status' => 'RCUR',
      'financial_type_id' => 1,
      'contributions' => [
        1 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        2 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        3 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        4 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        5 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        6 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        7 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        8 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        9 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        10 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        11 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        12 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'three failure in a row but the latest failure more than 12 months ago',
        ],
        13 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'three failure in a row but the latest failure more than 12 months ago',
        ],
        14 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'three failure in a row but the latest failure more than 12 months ago',
        ],
      ],
    ],
    5 => [
      'description' => 'Success full contributions and three failure but not in a row',
      'campaign' => 'Test Campaign',
      'amount' => 10,
      'iban' => 'NL84ARBN0847296849',
      'mandate_status' => 'RCUR',
      'financial_type_id' => 1,
      'contributions' => [
        1 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        2 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'three failure but not in a row',
        ],
        3 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        4 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        5 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'three failure but not in a row',
        ],
        6 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        7 => [
          'status' => self::STATUS_FAILED,
          'cancel_reason' => 'three failure but not in a row',
        ],
        8 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        9 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        10 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        11 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        12 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        13 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
        14 => [
          'status' => self::STATUS_COMPLETE,
          'cancel_reason' => '',
        ],
      ],
    ],
  ];

  private $campaignToIdList = [];

  public function generate() {
    $result = [];
    $today = new \DateTime();
    foreach($this->testData as $index => $testData) {
      $contact = civicrm_api3('Contact', 'create', [
        'contact_type' => 'Individual',
        'first_name' => 'Test',
        'last_name' => 'Test nr '.$index,
        'source' => $testData['description'],
      ]);
      $contactId = $contact['id'];
      $startDate = clone $today;
      $startDate->modify('-'.count($testData['contributions']).' months');
      $mandateParams = [
        'contact_id' => $contactId,
        'amount' => $testData['amount'],
        'iban' => $testData['iban'],
        'type' => 'RCUR',
        'status' => $testData['mandate_status'],
        'financial_type_id' => $testData['financial_type_id'],
        'start_date' => $startDate->format('Ymd'),
        'frequency_interval' => '1',
        'frequency_unit' => 'month'
      ];
      $contribParams = [
        'contact_id' => $contactId,
        'total_amount' => $testData['amount'],
        'financial_type_id' => $testData['financial_type_id'],
        'payment_instrument_id' => 'RCUR'
      ];
      if (!empty($testData['campaign'])) {
        $campaignId = $this->generateCampaign($testData['campaign']);
        $mandateParams['campaign_id'] = $campaignId;
        $contribParams['campaign_id'] = $campaignId;
      }
      $mandate = civicrm_api3('SepaMandate', 'createfull', $mandateParams);
      $mandate = reset($mandate['values']);
      $contribParams['contribution_recur_id'] = $mandate['entity_id'];
      $contributionDate = clone $today;
      foreach($testData['contributions'] as $contribIndex => $contribution) {
        $contribParams['contribution_status_id'] = $contribution['status'];
        $contribParams['receive_date'] = $contributionDate->format('Ymd');
        unset($contribParams['cancel_reason']);
        if (!empty($contribution['cancel_reason'])) {
          $contribParams['cancel_reason'] = $contribution['cancel_reason'];
        }
        civicrm_api3('Contribution', 'create', $contribParams);
        $contributionDate->modify('-1 month');
      }

      $result[$index] = [
        'contact_id' => $contactId,
        'mandate' => $mandate,
      ];
    }
    \Civi\SepaTerminateMandates\ConfigContainer::clearCache();
    return $result;
  }

  public function generateCampaign($campaign) {
    if (isset($this->campaignToIdList[$campaign])) {
      return $this->campaignToIdList[$campaign];
    }
    try {
      $campaignId = civicrm_api3('Campaign', 'getvalue', ['title' => $campaign, 'return' => 'id']);
      $this->campaignToIdList[$campaign] = $campaignId;
    } catch (\Exception $e) {
      // Do nothing
    }

    if (!isset($this->campaignToIdList[$campaign])) {
      $campaignId = civicrm_api3('Campaign', 'create', ['title' => $campaign]);
      $this->campaignToIdList[$campaign] = $campaignId;
    }
    return $this->campaignToIdList[$campaign];
  }

}
