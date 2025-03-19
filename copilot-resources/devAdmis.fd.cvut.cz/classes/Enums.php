<?php
# PHP has no enum < 8.2
# ty volr tak se rozbrec Pythonaku

abstract class Priorita_selecty{
    public const SELECT = [
    "dopravni_zatizeni",
    "spolufinancovani",
    "dopravni_vyznam",
    "technicky_stav",
    "stavebni_stav",
    "zivotni_prostredi",
    "regionalni_vyznam",
    "jedina_pristupova_cesta",
    "stav_pripravy",
    "hromadna_doprava",
    "nehodova_lokalita"
];
}

abstract class Priorita_dopravni_zatizeni{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Dopravní zatížení";
    public const FORM_NAME = "dopravni_zatizeni";
    public const VALUES = ["<500 (1)" => 1,
                            "<1000 (2)" => 2,
                            "<3000 (3)" => 3,
                            "<5000 (4)" => 4,
                            "<7000 (5)" => 5,
                            "<9000 (6)" => 6,
                            "<12000 (7)" =>7,
                            "<15000 (8)" =>8,
                            "<18000 (9)" => 9,
                            ">18000 (10)" => 10
];
}

abstract class Priorita_spolufinancovani{
    public const DISABLE_IN_PHASES = [6];
    public const NAME = "Spolufinancování";
    public const FORM_NAME = "spolufinancovani";
    public const VALUES = [
        "bez spolufinancování (0)" => 0,
        "<10 % (1)" => 1,
        "<20 % (2)" => 2,
        "<30 % (3)" => 3,
        "<40 % (4)" => 4,
        "<50 % (5)" => 5,
        "<60 % (6)" => 6,
        "<70 % (7)" =>7,
        "<80 % (8)" =>8,
        "<90 % (9)" => 9,
        "<100 % (10)" => 10

    ];
}

abstract class Priorita_dopravni_vyznam{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Dopravní význam";
    public const FORM_NAME = "dopravni_vyznam";
    public const VALUES = [
        "III. třída (1)" => 1,
        "III. třída (2)" => 2,
        "III. třída (3)" => 3,
        "III. třída (4)" => 4,
        "III. třída (5)" => 5,
        "III. třída (6)" => 6,
        "III. třída (7)" =>7,
        "II. třída (8)" =>8,
        "II. třída (9)" => 9,
        "II. třída (10)" => 10
    ];
}

abstract class Priorita_technicky_stav{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Technický stav";
    public const FORM_NAME = "technicky_stav";
    public const VALUES = [
        "novostavba (0)" => 0,
        "splňuje (1)" => 1,
        "splňuje s nedostatky (3)" => 3,
        "splňuje s výhradami (6)" => 6,
        "nesplňuje (10)" => 10
    ];
}

abstract class Priorita_stavebni_stav{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Stavební stav";
    public const FORM_NAME = "stavebni_stav";
    public const VALUES = [
        "novostavba (0)" => 0,
        "výborný (1)" => 1,
        "dobrý (2)" => 2,
        "vyhovující (3)" => 3,
        "nevyhovující (6)" => 6,
        "havarijní (10)" => 10
    ];
}

abstract class Priorita_zivotni_prostredi{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Životní prostředí";
    public const FORM_NAME = "zivotni_prostred";
    public const VALUES = [
        "nehodnoceno/most/bod.závada (0)" => 0,
        "bez vlivu (1)" => 1,
        "zlepší stav ŽP (10)" => 10
    ];
}

abstract class Priorita_regionalni_vyznam{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Regionální význam";
    public const FORM_NAME = "regionalni_vyznam";
    public const VALUES = [
        "nízký (2)" => 2,
        "střední (4)" => 4,
        "vysoký (7)" => 7,
        "velmi vysoký (10)" => 10
    ];
}

abstract class Priorita_jedina_pristupova_cesta{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Jediná přístupová cesta";
    public const FORM_NAME = "jedina_pristupova_cesta";
    public const VALUES = [
        "ne/bodová závada/novostavba (0)" => 0,
        "ano, jediná přístupová cesta (10)" => 10
    ];
}

abstract class Priorita_stav_pripravy{
    public const DISABLE_IN_PHASES = [6];
    public const NAME = "Stav přípravy";
    public const FORM_NAME = "stav_pripravy";
    public const VALUES = [
        "žádná (0)" => 0,
        "nízký (2)" => 2,
        "střední (5)" => 5,
        "vysoký (8)" => 8,
        "úplně připraveno (10)" => 10
    ];
}

abstract class Priorita_hromadna_doprava{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Hromadná doprava";
    public const FORM_NAME = "hromadna_doprava";
    public const VALUES = [
        "ne/bodová závada/novostavba (0)" => 0,
        "ano, využíváno HD (10)" => 10
    ];
}

abstract class Priorita_nehodova_lokalita{
    public const DISABLE_IN_PHASES = [];
    public const NAME = "Nehodová lokalita";
    public const FORM_NAME = "nehodova_lokalita";
    public const VALUES = [
        "bez nehod/most/novostavba (0)" => 0,
        "bez následků na zdraví (2)" => 2,
        "lehké následky na zdraví (4)" => 4,
        "těžké následky na zdraví (7)" => 7,
        "smrtelné následky (10)" => 10
    ];
}
