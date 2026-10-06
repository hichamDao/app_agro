/**
 * Fiches produit SEO de FoodMax Group (table product_seo) : textes reecrits.
 *
 * - Chaque fiche est rattachee a son "slug" : en executant ce fichier, les fiches
 *   existantes sont MISES A JOUR et celles qui manquent sont creees.
 * - Les textes restent generaux et factuels (culture, saison, conservation).
 *   Aucune certification, aucun pays livre, aucune region precise, aucun
 *   equipement n'y est affirme : completez ces champs vous-meme, uniquement
 *   avec des informations vraies et que vous pouvez prouver.
 * - Dans un champ, une barre verticale "|" separe les elements d'une liste, et
 *   un saut de ligne vide separe deux paragraphes.
 * - Les saisons sont indicatives : relisez-les avec votre calendrier reel.
 *
 * A executer APRES product_seo.sql, avec phpMyAdmin (onglet SQL).
 * Sauvegardez avant :  CREATE TABLE product_seo_backup AS SELECT * FROM product_seo;
 */

SET NAMES utf8mb4;

-- Tomatoes from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'tomatoes',
    'Tomatoes from Morocco',
    'Fresh Moroccan tomatoes, sorted by size and colour and packed to arrive firm and ready to sell.',
    'Morocco. A large share of the country''s winter and spring tomatoes are grown in greenhouses in the south, where the weather stays mild when it is cold elsewhere, and the warmer months bring field-grown fruit.',
    'Round tomatoes: the all-purpose tomato for slicing and salads | Cherry tomatoes: small and sweet, often sold in punnets | Plum tomatoes: firm and meaty, good for cooking | Truss tomatoes: sold on the vine | Coloured and specialty types: ask us what is available',
    'The main season generally runs from October to May, with the biggest volumes in the middle of winter. The exact dates change a little every year with the weather, so ask us what is available right now.',
    'Tomatoes are sorted by size and by colour stage, and we aim to match the range you specify. Cherry tomatoes are usually sold by the weight of the punnet rather than by size.',
    'Loose in cartons | Trays | Punnets and baskets for retail | Private label on request',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Tomatoes travel best at around 10 to 12 degrees, and never in a cold fridge, because below about 10 degrees they lose flavour and firmness. Depending on the distance and how soon you need them, the load goes by truck or by container.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'Tomatoes are hand-picked at the colour stage that suits the journey, then sorted so that size, colour and firmness are even in every box. Anything that does not match your specification is set aside.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Tomatoes: Types, Season and Export Guide',
    'Moroccan tomatoes for professional buyers: types, the October to May season, packing and how they travel. Ask FoodMax Group for a quote.',
    'published',
    '2026-05-10 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Oranges from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'oranges',
    'Oranges from Morocco',
    'Sweet, juicy Moroccan oranges, picked by hand and packed for the European season.',
    'Morocco, where mild winters, bright sun and cool nights suit citrus very well. Citrus is one of the most important crops of the country.',
    'Navel oranges: seedless and easy to peel, the classic orange for eating fresh | Blood oranges: red flesh and a winter speciality | Valencia oranges: late-season and very juicy, good for juice | Other varieties: ask us what is available',
    'Oranges are generally available from autumn to spring. Navels come in the middle of the season, blood oranges in winter and Valencias at the end, from spring into early summer. Dates vary with the weather each year.',
    'Oranges are graded by size, from smaller fruit for bags to large fruit for the shelf. Tell us the size you want and we will sort to it.',
    'Mesh bags | Cartons and trays | Retail packs | Loose in bulk, for processing or foodservice',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Citrus is a hardy fruit and travels well when it is kept cool and the air can move around it, which is why ventilated packing matters. We plan the load so the fruit arrives in good shape.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'Oranges are picked by hand when they are ripe, because they do not get sweeter after picking, then sorted by size, colour and appearance.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Oranges: Navel, Blood and Valencia Guide',
    'Navel, blood and Valencia oranges from Morocco: season, sizes, packing and how they travel. Ask FoodMax Group for availability and a quote.',
    'published',
    '2026-05-08 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Lemons from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'lemons',
    'Lemons from Morocco',
    'Fresh Moroccan lemons, aromatic and juicy, for retail, foodservice and processing.',
    'Morocco, where mild winters and plenty of sun suit lemon trees well.',
    'Eureka and Verna types: two lemons commonly grown around the Mediterranean | Seedless types: ask us what is available | Colour: from green to yellow, depending on the stage of harvest',
    'Lemons are available over a long period of the year, with the best supply generally in the cooler months. Ask us what is available right now.',
    'Lemons are graded by size, and we can sort to the range you ask for.',
    'Mesh bags | Cartons and trays | Retail packs | Bulk for foodservice',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Lemons like a little more warmth than other citrus, at around 10 to 13 degrees, and they are damaged by colder storage. Good ventilation around the fruit keeps them in good shape.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'Lemons are picked by hand and sorted for size, colour and skin. We set aside fruit that does not match your specification.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Lemons: Season, Sizes and Export Guide',
    'Fresh Moroccan lemons for professional buyers: season, sizes, packing and how to keep them. Ask FoodMax Group for availability and a quote.',
    'published',
    '2026-05-08 08:30:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Watermelon from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'watermelon',
    'Watermelon from Morocco',
    'Seeded, seedless and mini watermelons, picked ripe and loaded with care.',
    'Morocco, in the warm areas of the country where watermelon grows best.',
    'Seeded watermelon: the large traditional fruit | Seedless watermelon: popular in supermarkets | Mini watermelon: a small fruit for one or two people | Yellow-fleshed types: a specialty, ask us',
    'Watermelon is a warm-season crop. Early fruit comes from the warmest areas in spring and the main season runs through the summer, generally between March and August.',
    'Fruit weight varies a lot between types, from small mini watermelons to large traditional fruit. We sort by weight to your specification.',
    'Loose on pallets | Bulk bins | Cartons for mini watermelons | Individual labelling for retail',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Watermelons are heavy and bulky, so loading matters as much as temperature: the fruit needs to be well supported, with nothing pressing on it. Around 10 to 15 degrees is a good range, since storage that is too cold can damage the fruit.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'A watermelon does not ripen after it is cut, so it is picked only when it is ready, judged by the pale patch where it rested on the ground, the dried tendril near the stem and the hollow sound when tapped.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Watermelon: Season, Types and Packing Guide',
    'Seeded, seedless and mini watermelons from Morocco: season from spring to summer, packing and transport. Ask FoodMax Group for a quote.',
    'published',
    '2026-05-05 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Peppers from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'peppers',
    'Peppers from Morocco',
    'Crisp, colourful Moroccan peppers, graded carefully so every box is even.',
    'Morocco. Peppers are a warm-season crop and are often grown in greenhouses or under tunnels in the south, which gives steady conditions and a long harvest.',
    'Bell peppers: green, red, yellow and orange | Sweet pointed peppers | Mini sweet peppers | Chilli peppers: ask us what is available',
    'The main season generally runs from autumn to early summer. Ask us what is available right now.',
    'Peppers are sorted by size and by colour. Tell us the size range you prefer.',
    'Cartons | Trays | Retail packs | Mixed-colour packs on request',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Peppers keep best in a cool place, at around 7 to 10 degrees, and do not enjoy being stored too cold. Kept at the right temperature they stay fresh for a week or two.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'A green pepper is picked before it is ripe, while a red, yellow or orange one is left on the plant to ripen fully, which takes several more weeks and gives a sweeter, softer flavour. Peppers are picked by hand with a little stem attached to protect the fruit.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Peppers: Colours, Season and Export Guide',
    'Green, red, yellow and orange peppers from Morocco: season, sizes, packing and how they travel. Ask FoodMax Group for a quote.',
    'published',
    '2026-05-04 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Courgettes from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'courgettes',
    'Courgettes from Morocco',
    'Fresh Moroccan courgettes, firm and smooth, picked young and handled gently.',
    'Morocco, where courgettes are grown in greenhouses and in the open, depending on the time of year.',
    'Green courgettes (zucchini): the standard type | Light-green courgettes | Round and yellow types: ask us what is available',
    'Courgettes are generally available from autumn to late spring. Ask us what is available right now.',
    'Courgettes are graded by length and thickness. Tell us the range you need and we will sort to it.',
    'Cartons | Trays | Retail packs',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Courgettes are kept cool but not cold, because they do not like temperatures much below 7 degrees. Fresh, they stay at their best for about a week.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'Courgettes grow very fast and are harvested young, while they are tender and the seeds are small, so the fields are visited every day or two during the season. The skin is thin and marks easily, so they are picked and packed with care.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Courgettes: Season and Export Guide',
    'Fresh Moroccan courgettes for professional buyers: season, sizes, packing and handling. Ask FoodMax Group for availability and a quote.',
    'published',
    '2026-05-03 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Berries from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'berries',
    'Berries from Morocco',
    'Strawberries, blueberries and other berries, picked by hand and kept cold from the start.',
    'Morocco. Strawberries are grown mostly in the cooler north-west of the country, and other berries come from different areas depending on the season.',
    'Strawberries | Blueberries | Raspberries and blackberries: ask us what is available',
    'The berry season generally runs from winter into spring. Ask us what is available right now.',
    'Berries are sorted by size and appearance. Tell us the specification you prefer.',
    'Punnets | Trays | Retail packs | Private label on request',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Berries are the most delicate fruit we deal with. They should be cooled quickly after picking and kept close to freezing point, around 0 degrees, and they must stay dry, because moisture makes them go mouldy.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'Berries do not ripen after picking, so they are harvested by hand at the right colour, a few at a time, again and again through the season. They are handled as little as possible.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Berries: Strawberries and Blueberries Guide',
    'Strawberries, blueberries and other berries from Morocco: season, packing and the cold chain. Ask FoodMax Group for availability and a quote.',
    'published',
    '2026-05-02 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Dried Fruits from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'dried-fruits',
    'Dried Fruits from Morocco',
    'Dried fruits selected and packed with the same care as our fresh range.',
    'Morocco. The fruit is picked fully ripe and dried after harvest, and the range we can offer depends on the harvest.',
    'Dried figs and dates, depending on the harvest | Other dried fruits: ask us for the current range',
    'Dried fruit keeps, so it can be supplied across much of the year. The new harvest generally arrives from late summer into autumn.',
    'Sizes and grades depend on the product. Tell us what you need and we will explain what is available.',
    'Cartons | Bags | Retail packs | Bulk for processing',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Dried fruit keeps best in a cool, dry place, away from light and strong smells. Depending on the product, it does not always need a refrigerated vehicle, but a cool and dry load protects its quality.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'The fruit is dried until most of the water is gone, steadily and cleanly: too fast and it hardens on the outside, too slow and it can spoil. Selection before drying decides the quality afterwards.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Dried Fruits: Range and Export Guide',
    'Dried fruits from Morocco for professional buyers: range, season, packing and storage. Ask FoodMax Group for the current range and a quote.',
    'published',
    '2026-05-01 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Figs from Morocco
INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`, `packaging`, `availability`, `transportation`, `destinations`, `quality`, `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'figs',
    'Figs from Morocco',
    'Fresh figs, soft, sweet and fragile, picked by hand and packed with great care.',
    'Morocco, where the hot dry summers suit the fig tree very well.',
    'Fresh figs: ask us which types are available in the season | Dried figs: see our dried fruits guide',
    'Fresh figs are mostly harvested from late summer into autumn. Ask us what is available right now.',
    'Figs are sorted by size and appearance.',
    'Trays, in a single layer | Small punnets | Private label on request',
    'Supply follows the season. Tell us the quantity and how often you would like to receive it, and we will tell you honestly what we can commit to.',
    'Fresh figs are very delicate and have a short life, so they need careful packing in a single layer and an unbroken cold chain, at around 0 to 2 degrees.',
    'We supply professional buyers in Europe and in other markets. Tell us where the goods should go and we will explain what is possible, including labelling and documents.',
    'A fig does not ripen after it is picked, so it has to be harvested when it is ready, by hand and a few at a time, often several times a week during the season. The skin is thin and the fruit is soft, so it is handled gently.',
    'We do not list certificates on this page. If a certificate applies to a product or to a grower, we will tell you exactly which one it is and send you the document with your quote.',
    'Moroccan Figs: Fresh Fig Season and Export Guide',
    'Fresh Moroccan figs for professional buyers: season, packing and the cold chain. Ask FoodMax Group for availability and a quote.',
    'published',
    '2026-04-30 08:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `origin` = VALUES(`origin`),
    `varieties` = VALUES(`varieties`),
    `season` = VALUES(`season`),
    `sizes` = VALUES(`sizes`),
    `packaging` = VALUES(`packaging`),
    `availability` = VALUES(`availability`),
    `transportation` = VALUES(`transportation`),
    `destinations` = VALUES(`destinations`),
    `quality` = VALUES(`quality`),
    `certifications` = VALUES(`certifications`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);
