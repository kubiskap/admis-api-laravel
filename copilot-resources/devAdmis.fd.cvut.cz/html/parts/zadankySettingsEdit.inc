<?php
if (isset($_GET['zadanka']) && (is_numeric($_GET['zadanka']))) {
    $zadanka = getRequestType($_GET['zadanka']);
    } else {
    $zadanka['idRequestType'] = '"new"';
    $zadanka['name'] = '';
    $zadanka['formCode'] = '';
    $zadanka['configJson'] = '{}';
}
?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header card-header-danger card-header-icon">
                <div class="card-text">
                    <h4 class="card-title"><i class="material-icons">task</i> Editace žádanky
                    </h4>
                </div>
                <a href="nastaveni.php?sprava=zadanky" class="btn btn-light float-right" style="margin-top: 15px"><i class="fa fa-chevron-left"></i>Vrátit se na přehled žádanek (bez uložení)<div class="ripple-container"></div></a>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="zadankaName">Název žádanky</label>
                        <input required type="text" class="form-control" id="zadankaName" value="<?php echo $zadanka['name'] ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="zadankaCode">Kód žádanky</label>
                        <input required type="text" class="form-control" id="zadankaCode" value="<?php echo $zadanka['formCode']; ?>">
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <div id="dynamicZadankyEditFormHere"></div>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_GET['zadanka'])) {
    $customScripts = "
    <script>
$(document).ready(function () {
    var data = ".$zadanka['configJson'].";
$('#dynamicZadankyEditFormHere').formBuilder({
                    dataType: 'json',
                    formData: data,
                    i18n: {
                        override: {
          'en-US': {
            addOption: 'Přidat možnost',
          allFieldsRemoved: 'Všechna pole byla vymazána.',
          allowMultipleFiles: 'Dovolit uživatelům nahrát více souborů',
          autocomplete: 'Automatické doplnění',
          button: 'Tlačítko',
          cannotBeEmpty: 'Toto pole nesmí být prázdné',
          checkboxGroup: 'Zaškrtávací políčka',
          className: 'Třída',
          clearAllMessage: 'Opravdu chcete odstranit všechna políčka?',
          clear: 'Vymazat',
          close: 'Zavřít',
          content: 'Obsah',
          copy: 'Zkopírovat do schránky',
          copyButton: 'Kopírovat',
          copyButtonTooltip: 'Kopírovat',
          dateField: 'Datum',
          description: 'Nápověda pole',
          descriptionField: 'Popis',
          devMode: 'Vývojářský mód',
          editNames: 'Editovat jména',
          editorTitle: 'Formulářové prvky',
          editXML: 'Editovat XML',
          enableOther: 'Povolit &quot;Ostatní&quot;',
          enableOtherMsg: 'Povolit uživatelům přidávat své odpovědi',
          fieldNonEditable: 'Toto pole nelze upravit.',
          fieldRemoveWarning: 'Opravdu chcete odstranit toto pole?',
          fileUpload: 'Nahrání souboru',
          formUpdated: 'Formulář byl aktualizován',
          getStarted: 'Přetáhněte sem prvky z pravé části',
          header: 'Nadpis',
          hide: 'Upravit',
          hidden: 'Skryté pole vstupu',
          inline: 'Vložený',
          inlineDesc: 'Zobrazovat {type} jako vložený',
          label: 'Popisek',
          labelEmpty: 'Pole Popis musí být vyplněno',
          limitRole: 'Omezit přístup pro jednu nebo více z následujích rolí:',
          mandatory: 'Vyžadováno',
          maxlength: 'Maximální délka',
          minOptionMessage: 'Toto pole vyžaduje minimálně dvě možnosti',
          multipleFiles: 'Více souborů',
          name: 'Název',
          no: 'Ne',
          noFieldsToClear: 'Není co vymazat',
          number: 'Počet',
          off: 'Vypnuto',
          on: 'Zapnuto',
          option: 'Volba',
          options: 'Možnosti',
          optional: 'Volitelné',
          optionLabelPlaceholder: 'Popisek',
          optionValuePlaceholder: 'Hodnota',
          optionEmpty: 'Hodnota volby je poviná',
          other: 'Jiné',
          paragraph: 'Odstavec',
          placeholder: 'Ukázková hodnota (Placeholder)',
          'placeholder.value': 'Hodnota',
          'placeholder.label': 'Popisek',
          'placeholder.text': '',
          'placeholder.textarea': '',
          'placeholder.email': 'Vložte email',
          'placeholder.placeholder': '',
          'placeholder.className': 'Třídy oddělené mezerou',
          'placeholder.password': 'Vložte své heslo',
          preview: 'Náhled',
          radioGroup: 'Přepínač',
          radio: 'Přepínač',
          removeMessage: 'Odstranit element',
          removeOption: 'Odstarnit možnost',
          remove: '&#215;',
          required: 'Vyžadováno',
          richText: 'Rich Text Editor',
          roles: 'Access',
          rows: 'Řádky',
          save: 'Uložit',
          selectOptions: 'Možnosti',
          select: 'Vybrat ze seznamu',
          selectColor: 'Vyber barvu',
          selectionsMessage: 'Povolit výběr více možností',
          size: 'Velikost',
          'size.xs': 'Extra malé',
          'size.sm': 'Malé',
          'size.m': 'Výchozí',
          'size.lg': 'Velké',
          style: 'Style',
          styles: {
            btn: {
              'default': 'Default',
              danger: 'Danger',
              info: 'Info',
              primary: 'Primary',
              success: 'Success',
              warning: 'Warning'
            }
          },
          subtype: 'Type',
          text: 'Text',
          textArea: 'Víceřádkový text',
          toggle: 'Přepínač',
          warning: 'Varování!',
          value: 'Hodnota',
          viewJSON: '{  }',
          viewXML: '&lt;/&gt;',
          yes: 'Ano'
          }
        }
     },
     onSave: function saveData(evt, formData) {
         var requestName = $('#zadankaName').val();
         var requestCode = $('#zadankaCode').val();
         $.ajax({
            url: '/ajax/saveRequestFormTypeJson.php',
            type: 'POST',
            cache: false,
            data: {idRequestType: ".$zadanka['idRequestType'].", requestName, requestCode, formData},
            success: function (data, status) {
                if (status === 'success') {
                    console.log('Ulozeno')
                }
                if(data) {
            swal({
                title: 'Uloženo',
                text: 'Chcete se vrátit na přehled žádanek nebo pokračovat v úpravách?',
                type: 'success',
                showCancelButton: true,
                confirmButtonText: 'Vrátit na přehled',
                cancelButtonText: 'Pokračovat v úpravách'
            }).then((result) => {
                if (result) {
                    window.location.href = 'nastaveni.php?sprava=zadanky';
                } else {
                    if (data > 1) {
                        window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                    }
                }
            }).catch((res) => {
                if (data > 1) {
                        window.location.href = 'nastaveni.php?sprava=zadanky&zadanka='+data;
                    }
            });
        } else {
            swal({
                title: 'Chyba ukládání',
                text: 'Zkuste to za moment znovu. Pokud ani to nepomůže, kontaktujte administrátory aplikace.',
                type: 'danger'
            });
        }
            }
        });
                    }
});
});
</script>
";
}

?>