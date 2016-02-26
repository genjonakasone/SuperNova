<?php

/**
 * Class UBERound
 */
class UBERound {
  public $round_number = 0;

  public $round_outcome = UBE_COMBAT_RESULT_DRAW;

  /**
   * @var UBESnapshotUnit[][]
   */
  public $snapshot = array();

  /**
   * UBERound constructor.
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function __construct() {
  }

  /**
   *
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function __clone() {
  }

  /**
   * Делает снимок текущего состояния флота
   *
   * @param UBEFleetList $UBEFleetList
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function make_snapshot(UBEFleetList $UBEFleetList) {
    foreach($UBEFleetList->_container as $fleet_id => $UBEFleet) {
      foreach($UBEFleet->unit_list->_container as $unit_id => $UBEUnit) {
        $this->snapshot[$fleet_id][$unit_id] = new UBESnapshotUnit();
        $this->snapshot[$fleet_id][$unit_id]->init_from_UBEUnit($UBEUnit);
      }
    }
  }


  /**
   * Анализирует результаты раунда и генерирует данные для следующего раунда
   *
   * @param              $round
   * @param UBEFleetList $UBEFleetList
   *
   * @return UBERound
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  function sn_ube_combat_round_analyze($round, UBEFleetList $UBEFleetList) {
    $this->round_outcome = UBE_COMBAT_RESULT_DRAW;

    $nextRound = new UBERound();
    // $nextRound->init_from_previous_round($this);
    $nextRound->round_number = $this->round_number + 1;

    // $nextRound->fleet_combat_data->init_from_UBEFleetCombatList($this->fleet_combat_data, $UBEFleetList);
//    $outcome = $nextRound->fleet_combat_data->side_present_at_round_start;
    $UBEFleetList->actualize_sides();

    // Проверяем результат боя
    if(count($UBEFleetList->side_present_at_round_start) == 0 || $round >= 10) {
      // Если кого-то не осталось или не осталось обоих - заканчиваем цикл
      $this->round_outcome = UBE_COMBAT_RESULT_DRAW_END;
    } elseif(count($UBEFleetList->side_present_at_round_start) == 1) {
      // Если осталась одна сторона - она и выиграла
      $this->round_outcome = isset($UBEFleetList->side_present_at_round_start[UBE_PLAYER_IS_ATTACKER]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
    }

    return $nextRound;
  }

  /**
   * @param array        $sql_perform_ube_report_unit
   * @param              $unit_sort_order
   * @param UBEFleetList $UBEFleetList
   * @param array        $outer_prefix
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function sql_generate_unit_array(array $sql_perform_ube_report_unit, $unit_sort_order, UBEFleetList $UBEFleetList, array $outer_prefix) {
    foreach($this->snapshot as $fleet_id => $fleet_snapshot) {
      $inner_prefix = array(
        $UBEFleetList[$fleet_id]->owner_id,
        $fleet_id,
      );
      foreach($fleet_snapshot as $unit_id => $unit_snapshot) {
        $sql_perform_ube_report_unit[] = array_merge(
          $outer_prefix,
          $inner_prefix,
          $unit_snapshot->sql_generate_array(),
          array(
            $unit_sort_order++,
          )
        );
      }
    }
  }


  // REPORT ************************************************************************************************************
  //    REPORT LOAD ====================================================================================================
  /**
   * @param $report_unit_row
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function init_round_from_report_unit_row($report_unit_row) {
    $this->round_number = $report_unit_row['ube_report_unit_round'];
  }


  //    REPORT RENDER ==================================================================================================
  /**
   * @param UBE                 $ube
   * @param UBESnapshotUnit[][] $prevSnapshot
   *
   * @return array
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function report_render_round_fleet_list(UBE $ube, array $prevSnapshot) {
    global $lang;

    $fleet_list_template = array(
      UBE_PLAYER_IS_ATTACKER => array(),
      UBE_PLAYER_IS_DEFENDER => array(),
    );

    foreach($this->snapshot as $fleet_id => $fleet_snapshot) {
      $fleet_owner_id = $ube->fleet_list[$fleet_id]->owner_id;
      $planet_ube_row = $ube->fleet_list[$fleet_id]->UBE_PLANET;

      $template_fleet = array(
        'ID'          => $fleet_id,
        'IS_ATTACKER' => $ube->fleet_list[$fleet_id]->is_attacker == UBE_PLAYER_IS_ATTACKER,
        'PLAYER_NAME' => $ube->players[$fleet_owner_id]->player_name_get(true),
      );

      if(is_array($planet_ube_row)) {
        $template_fleet += $planet_ube_row;
        $template_fleet[PLANET_NAME] = $template_fleet[PLANET_NAME] ? htmlentities($template_fleet[PLANET_NAME], ENT_COMPAT, 'UTF-8') : '';
        $template_fleet['PLANET_TYPE_TEXT'] = $lang['sys_planet_type_sh'][$template_fleet['PLANET_TYPE']];
      }

      foreach($fleet_snapshot as $unit_id => $unit_snapshot) {
        $template_fleet['.']['ship'][] = $unit_snapshot->report_render_unit($prevSnapshot[$fleet_id][$unit_id]);
      }

      $fleet_list_template[$ube->fleet_list[$fleet_id]->is_attacker][] = $template_fleet;
    }

    return array_merge($fleet_list_template[UBE_PLAYER_IS_ATTACKER], $fleet_list_template[UBE_PLAYER_IS_DEFENDER]);
  }

  // REPORT ************************************************************************************************************
  //    REPORT LOAD ====================================================================================================
  /**
   * @param $report_unit_row
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function load_snapshot_unit_from_report_unit_row($report_unit_row) {
    $fleet_id = $report_unit_row['ube_report_unit_fleet_id'];
    $unit_id = $report_unit_row['ube_report_unit_unit_id'];

    $this->snapshot[$fleet_id][$unit_id] = new UBESnapshotUnit();
    $this->snapshot[$fleet_id][$unit_id]->init_from_report_unit_row($report_unit_row);
  }

}