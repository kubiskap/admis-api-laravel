<?php
/**
 * Created by PhpStorm.
 * User: Petros
 * Date: 27.01.2023
 * Time: 15:34
 */

// TODO: Přepsat na export priorit!

require_once(__DIR__."/../../classes/PhpSpreadsheet/vendor/autoload.php");
require_once __DIR__."/../conf/config.inc";
require_once SYSTEMINCLUDES."authenticateUser.php";
overUzivatele($pristup_zakazan);

$doc = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $doc->getActiveSheet();
$doc->getProperties()
    ->setCreator($_SESSION['jmeno'])
    ->setLastModifiedBy($_SESSION['jmeno'])
    ->setTitle("Výpis projektů")
    ->setDescription("Výpis projektů vytvořených v aplikaci ADMISS");
$doc->setActiveSheetIndex(0);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(1);
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($doc);
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );
$headerContetnt = [
    'ID Projektu',
    'Název stavby',
    'Předmět stavby',
    'Fáze',
    'Druh stavby',
    'Poddruh stavby',
    'Zdroj financování',
    'Dopravní zatížení',
    'Spolufinancování',
    'Dopravní význam',
    'Technický stav',
    'Stavební stav',
    'Životní prostředí',
    'Regionální význam',
    'Jediná přístupová cesta',
    'Stav přípravy',
    'Hromadná doprava',
    'Nehodová lokalita',
    'Výsledné hodnocení',
    'Hodnocení s korekcí'
];
$sheet->fromArray($headerContetnt,null,'A1');

$styleBoldClumns = [
    'font' => [
        'bold' => true,
    ]];
$styleArrayFirstRow = [
    'font' => [
        'bold' => true,
    ],
    'borders' => [
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'FCF55F',
        ],
    ],
];

$stylePrePrices = [
    'font' => [
        'color' =>[
            'rgb' => 'E8403C',
        ],
    ]
];


$styleDate = [
    'numberFormat' => [
        'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY
    ]
];

$styleVerticalAlign = [
    'alignment' => [
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ]
];

$sheet->getStyle('A1:'.$sheet->getHighestColumn()."1")->applyFromArray($styleArrayFirstRow);

$headerCollums = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn(). $sheet->getHighestRow());

foreach ($sheet->getColumnIterator('A',$sheet->getHighestColumn()) as $column){
    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
}

$_GET['projectsOrder'] = 'project_type_asc';
$projects = getFilteredProjects($_GET,10000,1);


$content = array();
$prePriceRows = array();
$line = 1;
foreach ($projects as $idProject){

    $line++;
    $project = new Project($idProject['idProject']);
    if($project->baseInformation['priorityAtts'] != null) {
        $priority = new Priority($project->getId(), json_decode($project->baseInformation['priorityAtts'], true));
    }

    $row = array(
        $project->getId(),
        $project->baseInformation['name'],
        $project->baseInformation['subject'],
        $project->baseInformation['phaseName'],
        $project->baseInformation['projectTypeName'],
        $project->baseInformation['projectSubtypeName'],
        $project->getFinSource(),

    );
    require_once(CLASSES. "Enums.php");
    if($project->baseInformation['priorityAtts'] != null) {
        foreach ($priority->getAtts() as $priorityValue) {
            array_push($row, $priorityValue);
        }
    } else {
        foreach (Priorita_selecty::SELECT as $eachSelect) {
            array_push($row, 'NEHODNOCENO');
        }
    }
    if($project->baseInformation['priorityAtts'] != null) {
        array_push($row, round($priority->getResult(), 2));
        array_push($row, round($priority->getCorrectionValue() * 1000, 2));
    } else {
        array_push($row, 'NEHODNOCENO');
        array_push($row, 'NEHODNOCENO');
    }
    array_push($content, $row);


}

$sheet->fromArray($content,null,'A2');

// pevná šířka sloupců
$sheet->getColumnDimension('A')->setAutoSize(false);
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setAutoSize(false);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setAutoSize(false);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setAutoSize(false);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setAutoSize(false);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setAutoSize(false);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setAutoSize(false);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setAutoSize(false);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setAutoSize(false);
$sheet->getColumnDimension('I')->setWidth(15);
$sheet->getColumnDimension('J')->setAutoSize(false);
$sheet->getColumnDimension('J')->setWidth(15);
$sheet->getColumnDimension('K')->setAutoSize(false);
$sheet->getColumnDimension('K')->setWidth(15);
$sheet->getColumnDimension('L')->setAutoSize(false);
$sheet->getColumnDimension('L')->setWidth(15);
$sheet->getColumnDimension('M')->setAutoSize(false);
$sheet->getColumnDimension('M')->setWidth(15);
$sheet->getColumnDimension('N')->setAutoSize(false);
$sheet->getColumnDimension('N')->setWidth(15);
$sheet->getColumnDimension('O')->setAutoSize(false);
$sheet->getColumnDimension('O')->setWidth(15);
$sheet->getStyle('B1:'.'B'.$sheet->getHighestRow())->getFont()->setBold(true);
$sheet->getStyle('S1:'.'S'.$sheet->getHighestRow())->getFont()->setBold(true);
$sheet->getStyle('T1:'.'T'.$sheet->getHighestRow())->getFont()->setBold(true);

$sheet->getColumnDimension('P')->setAutoSize(false);
$sheet->getColumnDimension('P')->setWidth(15);
$sheet->getColumnDimension('Q')->setAutoSize(false);
$sheet->getColumnDimension('Q')->setWidth(15);
$sheet->getColumnDimension('R')->setAutoSize(false);
$sheet->getColumnDimension('R')->setWidth(15);
$sheet->getColumnDimension('S')->setAutoSize(false);
$sheet->getColumnDimension('S')->setWidth(15);
$doc->getActiveSheet()->setAutoFilter('A1:S1');



foreach ($sheet->getRowIterator() as $key => $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE);
    $sheet->getRowDimension("$key")->setRowHeight(15);
    foreach ($cellIterator as $cell) {
        $cell->getStyle()->applyFromArray($styleVerticalAlign);
    }
}

for ($i = 2; $i < ($sheet->getHighestRow()); $i++) {
    if ($i % 2 == 0) {
        $doc->getActiveSheet()->getStyle('A' . $i . ':T' . $i)->applyFromArray(
            array(
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFFDD0',
                    ]]
            )
        );
    }
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="ADMIS-export-priorit-projektu.xlsx"');
$writer->save("php://output");

