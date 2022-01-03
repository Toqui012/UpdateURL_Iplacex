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
 * Forma del bloque
 *
 * @package   block_updateurl
 * @copyright 2021 su nombre
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_updateurl_edit_form extends block_edit_form {
        
    protected function specific_definition($mform) {
        
        // Sección de configuración de parámetros.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Una cadena de texto a desplegarse.
        $mform->addElement('text', 'config_text', get_string('blockstring', 'block_updateurl'));
        $mform->setDefault('config_text', 'Cadena por omisión');
        $mform->setType('config_text', PARAM_RAW);   
        
        // Crear el parámetro para el título del bloque
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_updateurl'));
        $mform->setDefault('config_title', 'Valor por default');
        $mform->setType('config_title', PARAM_TEXT);

        // Ocultar
        $mform->addElement(
            'advcheckbox', 
            'config_disabled', 
            get_string('blockdisabled', 'block_updateurl'), 
            'Desactivar el bloque'
        );


    }
}