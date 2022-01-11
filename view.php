<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * view.php tiene el control (la lógica) de la página
 *
 * @package   block_updateurl
 * @copyright 2021 su nombre
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('updateurl_form.php');
require_once($CFG->libdir . '/csvlib.class.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->requires->jquery();
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/blocks/updateurl/test.js'));

// Verifique todas las variables requeridas
$courseid = required_param('courseid', PARAM_INT);

// Busca el identificador del bloque
$blockid = required_param('blockid', PARAM_INT);

// Busca si hay más variables 
$id = optional_param('id', 0, PARAM_INT);

// Busca la id de la importación del archivo
$importid = optional_param('importid', '', PARAM_INT);

// Busca la cantidad de filas seleccionadas dentro del formulario
$previewrows = optional_param('previewrows', 10, PARAM_INT);


// Devuelve un unico registro de la base de datos como un objeto
// donde se cumplen todas las condiciones dadas
if (!$course = $DB->get_record('course',array('id' => $courseid))) {
    print_error('invalidcourse', 'block_updateurl', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/updateurl/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_updateurl'));

// Creamos el nodo del bloque en las migas de pan
$settingsnode = $PAGE->settingsnav->add(get_string('updateurlsettings', 
    'block_updateurl'));
// Creamos la URL del bloque con el id del bloque
$editurl = new moodle_url('/blocks/updateurl/view.php', 
    array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
// Añadimos el nodo con la url del bloque
$editnode = $settingsnode->add(get_string('editpage', 'block_updateurl'), $editurl);
// Activamos las migas de pan
$editnode->make_active();

// Instancia del formulario
$updateurl = new updateurl_form();
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$updateurl->set_data($toform);
$returnurl = new moodle_url('/course/view.php');


if($updateurl->is_cancelled()) {
    // Los formularios cancelados redirigen a la página principal del curso.
    $updateurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);

} else if (empty($importid)){
    if ($fromform = $updateurl->get_data()) {
        $importid = csv_import_reader::get_new_iid('block_updateurl');
        $cir = new csv_import_reader($importid, 'block_updateurl');
        $content = $updateurl->get_file_content('filename');
        $readcount = $cir->load_csv_content($content, $fromform->encoding, $fromform->delimiter);

        // unset($content);

        // if ($readcount === false) {
        //     print_error('csvfileerror', 'tool_uploadcourse', $returnurl, $cir->get_error());
        // } else if ($readcount == 0){
        //     print_error('csvemptyfile', 'error', $returnurl, $cir->get_error());
        // }

        $user = $DB->get_record_sql('SELECT id, fullname FROM mdl_course WHERE id = 2;');
        // print_r($user->{'fullname'});

        $sql = "UPDATE {course}
                    SET fullname = 'Ha funcionado'
                WHERE id = 2";

        
        // if (strpos($content, $user->{'fullname'})) {
        //     $DB->execute($sql, $params=null);
        //     print_r('hola mundo');
        // }
        
    } else{
         // Primera vez o con errores
        // $site = get_site();
        // Desplegamos nuestra página
        echo $OUTPUT->header();
        $updateurl->display();
        echo $OUTPUT->footer();
        die();
    }
} else {
    $cir = new csv_import_reader($importid, 'uploadcourse');
}


// Data to set in the form.
// $data = array('importid' => $importid, 'previewrows' => $previewrows);
// if (!empty($fromform)) {
//     foreach ($fromform -> options as $key => $value) {
//         $data["options[$key]"] = $value;
//     }
// }
// $context = context_system::instance();
// $fromform2 = new 
