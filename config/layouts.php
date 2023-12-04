<?php
/**
 * Defining the file paths, names, and patterns for generating output files for different sections or entities within the system.
 *
 * @param string layoutPath: Path to the XML layout file for the section.
 * @param string outputPath: Path where output files for the section will be stored.
 * @param string outputZipPath: Path where zip files for the section will be stored.
 * @param string outFileName: Output file name pattern for the section, which may contain placeholders like {{ StringToReplace }}.
 * @param string outFileNameZip: Output zip file name pattern for the section, which may contain placeholders like {{DATE(dm)}}.
 *
 * @return array
*/

return [
    'EXAMPLE' => [
        'layoutPath'        => './files/layouts/LayoutExample.xml',
        'outputPath'        => './files/output/EXAMPLE/arquivos',
        'outputZipPath'     => './files/output/EXAMPLE/',
        'outFileName'       => '{{FILIAL}}_{{DATE(dm)}}_{{UF}}.txt',
        'outFileNameZip'    => '',
    ],
];