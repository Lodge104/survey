<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * LimeSurvey
 * Copyright (C) 2007-2019 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v3 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/* 
WARNING!!!
ONCE SET, ENCRYPTION KEYS SHOULD NEVER BE CHANGED, OTHERWISE ALL ENCRYPTED DATA COULD BE LOST !!!

*/

$config = array();
$config['encryptionkeypair'] = '728800183d77705b84f87eb77c406d06359a82dc704eb037f168601ce92c4da14b8f412d80ba060e4344ce5ecec8eee2ca7a92398b4cf442fde8254bd85bbbaf4b8f412d80ba060e4344ce5ecec8eee2ca7a92398b4cf442fde8254bd85bbbaf';
$config['encryptionpublickey'] = '4b8f412d80ba060e4344ce5ecec8eee2ca7a92398b4cf442fde8254bd85bbbaf';
$config['encryptionsecretkey'] = '728800183d77705b84f87eb77c406d06359a82dc704eb037f168601ce92c4da14b8f412d80ba060e4344ce5ecec8eee2ca7a92398b4cf442fde8254bd85bbbaf';
return $config;