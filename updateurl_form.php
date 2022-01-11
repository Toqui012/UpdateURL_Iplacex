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
 * Bloque de saludo al mundo: la vista de los campos
 *
 * @package   block_updateurl
 * @copyright 2021 su nombre
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("{$CFG->libdir}/formslib.php");

class updateurl_form extends moodleform {
    
    function definition() {
        
        // Front Section

        $mform =& $this->_form;
       // elementos ocultos
        $mform->addElement('hidden', 'blockid');
        $mform->setType('blockid', PARAM_RAW);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_RAW);

        $mform->addElement('header','displayinfo', get_string('textfields', 'block_updateurl'));

        // Elemento de texto
        // $mform->addElement('text', 'pagetitle', get_string('pagetitle', 'block_updateurl'));
        // $mform->setType('pagetitle', PARAM_RAW);
        // $mform->addRule('pagetitle', null, 'required', null, 'client');


        // Elemento text area con HTML
        // $mform->addElement('editor', 'displaytext', get_string('displayedhtml', 'block_updateurl'));
        // $mform->setType('displaytext', PARAM_RAW);
        // $mform->addRule('displaytext', null, 'required', null, 'client');

       
        // Filepicker aún falta decidir
        $test = $mform->addElement(
            'filepicker', 
            'filename', 
            get_string('file'), 
            null, 
            array('accepted_types' => '.csv')
        );
        $mform->addRule('filename', null, 'required', null, 'client');
        $mform->addHelpButton('filename','coursefile','tool_uploadcourse');

        // Codigo codificación
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', 
                get_string('encoding', 'tool_uploadcourse'), $choices);
        $mform->setDefault('encoding','UTF-8');
        $mform->addHelpButton('encoding','encoding','tool_uploadcourse');


        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter', 
                get_string('delimiter', 'block_updateurl'), $choices);
        $mform->addHelpButton('delimiter','csvdelimiter','tool_uploadcourse');

        $choices = array('10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '10000' => 10000);
        $mform->addElement('select', 'previewrows', 
                get_string('rowpreviewnum', 'tool_uploadcourse') ,$choices);
        $mform->setType('previewrows', PARAM_INT);
        $mform->addHelpButton('previewrows', 'rowpreviewnum', 'tool_uploadcourse');

        $mform->addElement('hidden', 'showpreview', 1);
        $mform->setType('showpreview', PARAM_INT);

        // $mform->addElement('html', '<h1 id="hw">Hola Mundo!</h1>');
       
        // Codigo de Prueba
        // $csv = 'C:\\xampp\\htdocs\\test.csv';
        // $file = fopen($csv, 'r');
        // $contentfile = file_get_contents($csv);
        // $select = $mform->addElement('select', 'delimiter', get_string('delimiter', 'block_updateurl'), array($variable), $attributes);


        // $fh = fopen($csv,'r');
        // while(list($name, $lastname, $email) = fgetcsv($fh,1024,','))
        // {
            // $select = $mform->addElement('select', 'delimiter', get_string('delimiter', 'block_updateurl'), array($name), $attributes);
            // $mform->addElement('html', '<h1>'+ $name +'</h1>');
        // }

        // Botones del sistema
        $this->add_action_buttons();
    }
}