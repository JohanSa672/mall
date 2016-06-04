Nereus-base
==================

Nereus är en katalogstruktur gjord i php för att göra strukturerade webbplatser. 

Jag tog namnet Nereus efter en havsgud i grekisk mytologi. Han var son till Pontos (Havet) och Gaia (Jorden),
gift med Doris och far till en lång rad havsnymfer (50 eller 100) som efter honom kallades för
nereiderna; till dem hörde Amfitrite, Galatea och Thetis.

Katalogstrukturen är inte speciellt djup.

Nereus består av mapparna src, theme, webroot och filerna gitignore, LICENSE och denna README-sida.
I mappen SRC finns det kod som är återanvändbar och i mappen finns mappar där klasserna finns. Klasser som finns här är CBlog,CContent,CDatabase,
CGallery mfl samt bootstrap.php. Dem har du nytta av om du vill göra en blogg, vissa bilder i ett galleri mm.
Viktigast är CUser och CImage där inloggningen finns och CImage som används tillsammans med img.php som finns i webroot för att visa bilder.
Bootstrap är en fil som autoladdar alla klasser.

Theme består av tre php filer. functions.php, index.tpl.php och render.php. I index.tpl.php finns grundstrukturen för hur hemsidan ser ut, presentationen.
Själva genereringen av html-sidan finns där, dvs.html-kodningen med head,meta, body och footer m.m.
I render.php inkluderas function.php och index.tpl.php. Själva huvudfunktionen finns i index.tpl.php som återger data i nereus-arrayen till variabler. 
I function.php läggs funktioner som man vill använda i Nereus-strukturen.

I webroot finns cache, css, img, js, configure.php,img.php,.htaccess,favicon,humans,robots,sitemap och alla sidecontrollers.
I cache lagras alla cacheade bilder. I CSS finns stylesheeten som stylar hemsidan, i mappen img finns bilderna lagrade och i mappen js kan man lägga javascripten.
configure.php,img.php finns också i webrooten. I konfigurations filen finns information om hur felrapporteringen, inloggning till databasen, start av session,
konstanter. m.m samt uppbyggnaden av drop-down navigationsmenyn. Observera att konfigurationsfilen alltid måste finnas med vid varje ny sidekontroller som byggs. 
Det finns med en hello.php fil som visar hur en sidcontroller fungerar. hello.php skapas genom att arrayen Nereus[] matas in med parametrarna("keys") title, header, main,
footer som är grundstrukturen och som sedan lämnas över till temahanteringen som skapar sidan. Sitemap.xml är en lista över de länkar som webbplatsen innehåller.
I robots kan viss informatio spärras.

Utan för dess tre mapparna src, theme och webroot finns licensefilen, .gitignore och denna readme-sida.



License 
------------------
 
This software is free software and carries a MIT license.
 
 
------------------
 
Copyright (c) 2013 Mikael Roos /Johan Salomonsson