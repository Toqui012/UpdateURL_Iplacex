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
require_once($CFG->dirroot.'/blocks/updateurl/lib.php');
require_once($CFG->dirroot.'/blocks/updateurl/classes/FileClass.php');

global $DB, $OUTPUT, $PAGE, $USER;

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

// Recuperar el parametro viewpage
$viewpage = optional_param('viewpage', false, PARAM_BOOL);


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
    redirect($updateurl, 'El formulario ha sido cancelado', null, \core\output\notification::NOTIFY_ERROR);

} else if (empty($importid)){
    if ($fromform = $updateurl->get_data()) {

        // Get Data
        $importid = csv_import_reader::get_new_iid('block_updateurl');
        $cir = new csv_import_reader($importid, 'block_updateurl');
        $content = $updateurl->get_file_content('filename');
        $readcount = $cir->load_csv_content($content, $fromform->encoding, $fromform->delimiter);

        if ($readcount === false) {
            print_error('csvfileerror', 'tool_uploadcourse', $returnurl, $cir->get_error());
        } else if ($readcount == 0){
            print_error('csvemptyfile', 'error', $returnurl, $cir->get_error());
        }

        //File Source
        //Se busca el archivo subido dentro del directorio temporal que establece moodle
        //Se abre el archivo para poder procesar la data y transofrmarla

        $pathToOpen = "C:\\xampp\\moodledata\\temp\\csvimport\\block_updateurl\\2\\$importid";
        $file = fopen($pathToOpen, 'r');


        // Consulta a base de datos
        
        try {
            while(list($courseid, $find, $replace) = fgetcsv($file,10000, $fromform->delimiter))
            {
                // Se omiten los encabezados del csv (en el caso que los tenga)
                if ($courseid != 'courseid' && $find != 'find' && $replace != 'replace') {
    
                    $queryCourse = "SELECT c.id, c.fullname, u.externalurl
                                    FROM mdl_course c 
                                    INNER JOIN mdl_url u ON c.Id = u.Course";
    
                    $dataCourse = $DB->get_records_sql($queryCourse, null);

                    // Definición del arreglo encargado de guardar los registros que contengan errores
                    $arrayError[] = array();

                    // Se hace el match de los datos preguntando si son coincidentes
                    if ($courseid == $dataCourse[$courseid]->id && trim($find) == trim($dataCourse[$courseid] ->externalurl)) {
                        
                        /* Una vez verificada la compración entre los datos de la bd y csv 
                        se procede a generar y ejecutar la sentencia para actualizar las url */

                        $sql = "UPDATE mdl_url
                                    SET externalurl = '$replace'
                                WHERE course = $courseid";
                        $DB->execute($sql, $params=null);

                        /* Se crea un nuevo objeto para crear el registro historico en la base de datos */
                        $newRegisterFile = new FileClass();
                        $newRegisterFile-> userid = $USER->id;
                        $newRegisterFile-> courseid = $courseid;
                        $newRegisterFile-> oldurl = $find;
                        $newRegisterFile-> newurl = $replace;
                        $newRegisterFile-> numupdate = 1;
                        $newRegisterFile-> timemodified = strtotime(date('d-m-Y'));
                        
                        if (! $DB -> insert_record('block_updateurl', $newRegisterFile)) {
                            print_error('inserterror', 'block_updateurl');
                        }
                    }
                    else {
                        array_push($arrayError, $courseid, $find, $replace);
                    }
                }
            }
            
            
            // Return Section 
            echo $OUTPUT->header();
            if ($viewpage) {
                $updateurlpage = $DB->get_record('block_updateurl', array('id' => $id));
                block_update_print_page($updateurlpage);
            } else {
                
                if ($arrayError[2]){
                    // redirect('http://localhost/moodle/?redirect=0', "El formulario se ha completado pero con errores dentro del archivo adjuntado <br>", read_list_updateurl_ERROR($arrayError) ,"<br>", 
                    // null, \core\output\notification::NOTIFY_ERROR);
                    print_error("Los datos se han actualizado correctamente pero no en su totalidad, verifique los siguientes registros", read_list_updateurl_ERROR($arrayError));
                }
                else
                {
                    // $updateurl->display();
                    redirect('http://localhost/moodle/?redirect=0', "La opreación se ha llevado a cabo exitosamente", 
                    null, \core\output\notification::NOTIFY_SUCCESS);
                }

            }
            echo $OUTPUT->footer();

        } catch (\Throwable $th) {
            throw $th;
        }
       
    } else{
        
        // Primera vez o con errores
        echo $OUTPUT->header();
        if ($viewpage) {
            $updateurlpage = $DB->get_record('block_updateurl', array('id' => $id));
            block_update_print_page($updateurlpage);
        } else {
            $updateurl->display();
        }
        echo $OUTPUT->footer();
    }
} else {
    $cir = new csv_import_reader($importid, 'uploadcourse');
}