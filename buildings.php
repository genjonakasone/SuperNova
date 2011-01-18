<?php

/**
 * buildings.php
 *
 *  Allow building of... hmm... buildings
 *
 * @version 1.3s Security checks by Gorlum for http://supernova.ws
 * @version 1.3
// History version
// 1.0 - Nettoyage modularisation
// 1.1 - Mise au point, mise en fonction pour linéarisation du fonctionnement
// 1.2 - Liste de construction batiments
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

$mode = sys_get_param_escaped('mode');

includeLang('buildings');
includeLang('infos');

$IsWorking = HandleTechnologieBuild ( $planetrow, $user );

$que = PlanetResourceUpdate($user, $planetrow, $time_now);

switch ($mode)
{
  case 'fleet':
    // --------------------------------------------------------------------------------------------------
    FleetBuildingPage ( $planetrow, $user, $que );
  break;

  case 'research':
    // --------------------------------------------------------------------------------------------------
    ResearchBuildingPage ( $planetrow, $user, $IsWorking['OnWork'], $IsWorking['WorkOn'], $que);
  break;

  case 'defense':
    // --------------------------------------------------------------------------------------------------
    DefensesBuildingPage ( $planetrow, $user, $que );
  break;

  case QUE_STRUCTURES:
  case 'buildings':
  default:
    eco_build(QUE_STRUCTURES, $user, $planetrow, $que);
  break;
}

?>
