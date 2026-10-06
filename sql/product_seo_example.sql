/**
 * Donnees d'exemple pour la table product_seo.
 *
 * 9 produits avec leur fiche SEO complete : origine, varietes, saison,
 * tailles, conditionnement, disponibilite, transport, marches de destination,
 * qualite, certifications.
 *
 * Chaque slug correspond a une URL publique :
 *   /products/tomatoes, /products/oranges, etc.
 *
 * Executer APRES product_seo.sql.
 */

INSERT INTO `product_seo`
    (`slug`, `title`, `subtitle`, `origin`, `varieties`, `season`, `sizes`,
     `packaging`, `availability`, `transportation`, `destinations`, `quality`,
     `certifications`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES

(
    'tomatoes',
    'Tomatoes from Morocco',
    'Fresh Moroccan tomatoes, sorted by size and color, packed for European markets.',
    'Morocco — Souss-Massa and Meknes-Saïss regions, grown in greenhouse and open field.',
    'Cherry tomatoes (red, yellow, striped) | Round plum tomatoes (Roma) | Colored tomatoes (Kumato purple, Bamano yellow)',
    'October to May. Peak volumes: January to March.',
    'Cherry: 18–22mm diameter | Round: 4–6cm diameter | Plum: 5–7cm length',
    'Shaker 250g (10x250g bottles/tray) | Bucket 500g (12x500g punnets/crate) | Loose 4kg carton | Tray 250g (15x250g)',
    'Weekly shipments available. Container loads (20ft/40ft) and LCL options.',
    'Packed at 10–12°C. Transported in refrigerated 40" high-cube containers with continuous temperature monitoring.',
    'France, Spain, Germany, Netherlands, UK. Also shipping to the Gulf and West Africa.',
    'Hand-harvested at peak ripeness. Hydrocooled within 2 hours. Optical sorting removes any misshapen fruit.',
    'GlobalGAP, ISO 22000, BRC, IFS. EU phytosanitary certificate included with every shipment.',
    'Moroccan Tomatoes: Varieties, Season & Export Guide',
    'Fresh Moroccan tomatoes packed and exported to Europe. Varieties, harvest season, packaging and cold chain details.',
    'published',
    '2026-05-10 08:00:00',
    '2026-05-10 08:00:00'
),

(
    'oranges',
    'Oranges from Morocco',
    'Sweet Moroccan oranges, exported in ventilated containers throughout the European season.',
    'Morocco — Citrus Saiss NV (Saïss plain) and Tadla-Azilal region. Sandy-limestone soils.',
    'Navel oranges (Washington Navel, Late Newham) | Blood oranges (Moro, Tarocco) | Esla (early season)',
    'November to April. Navel peak: December–March. Blood orange peak: January–February.',
    'Class I: 85–95mm diameter (large) | Class II: 75–85mm (medium) | Class III: 65–75mm (standard)',
    '8kg mesh bags | 4kg cardboard trays | 1.5kg clamshells (retail) | 1kg flow-wrap punnets | Bulk bins (foodservice)',
    'Year-round supply of seedless varieties (Eurogold) from October to March.',
    'Pre-cooled to 4–6°C. Shipped in refrigerated containers at 2–4°C with ethylene absorption pads.',
    'France, Spain, Germany, Netherlands, UK, Italy, Belgium, Luxembourg, Scandinavia.',
    'Fruit is tree-ripened before harvest. Internal quality checked by Brix measurement (>11°). External sorting includes weight, color, and blemish detection.',
    'GlobalGAP, ISO 22000, BRC, IFS, EU Organic (on request). Integrated pest management (IPM) with pheromone traps.',
    'Moroccan Oranges: Navel and Blood Orange Export Guide',
    'Sweet Moroccan oranges exported to Europe. Varieties, harvest calendar, packaging and shipping details.',
    'published',
    '2026-05-08 08:00:00',
    '2026-05-08 08:00:00'
),

(
    'lemons',
    'Lemons from Morocco',
    'Premium Moroccan lemons, thin-skinned and aromatic, available year-round.',
    'Morocco — Tadla-Azilal and Rif regions. Mild Mediterranean climate with cool, wet winters.',
    'Eureka lemons (year-round) | Europagold (seedless, October–March) | Verna (late-season, April–May)',
    'Eureka: November to April (peak). Europagold: October to March. Verna: April to May.',
    'Class I: 55–70mm diameter (large) | Class II: 45–55mm (medium) | Class III: 35–45mm (standard)',
    '6kg mesh bags | 4kg cardboard trays | 1kg flow-wrap punnets | Bulk bins (12kg) for foodservice',
    'Continuous supply from October to April, with limited volumes of Verna in May–June.',
    'Stored at 2–4°C. Exported in refrigerated 40" containers with ethylene absorption and CO2 scrubbing.',
    'France, Spain, Germany, Netherlands, UK, Portugal, Scandinavia, Middle East (UAE, Saudi Arabia).',
    'Harvested by hand with half the leaf for natural fragrance. Fruit is washed, sorted by size and color, and waxed.',
    'GlobalGAP, ISO 22000, BRC, IFS. IPM-certified growers. EU phytosanitary certificate per shipment.',
    'Moroccan Lemons: Eureka and Seedless Export Guide',
    'Premium Moroccan lemons, thin-skinned and aromatic. Varieties, season, packaging and export markets.',
    'published',
    '2026-05-08 09:00:00',
    '2026-05-08 09:00:00'
),

(
    'watermelon',
    'Watermelon from Morocco',
    'Sweet Moroccan watermelons, grown for export with continuous cold chain protection.',
    'Morocco — Souss-Massa and Meknes regions. Sandy soil with controlled irrigation.',
    'Seedless: Sugar Tripplesweet, Mini Love | Seeded: Crimson Sweet, Yellow Crimson',
    'March to August. Peak: May–June.',
    'Seedless: 3–5kg (Mini Love), 7–11kg (Sugar Tripplesweet) | Seeded: 6–9kg (Crimson Sweet), 5–8kg (Yellow Crimson)',
    'Individual poly sleeves | Half-trays (6–8 fruits/master case) | Bulk bins (12kg) | Retail clamshells (3 per master case)',
    'Weekly shipments from March to August, with extended season (October–December) for storage varieties.',
    'Hydrocooled to 10–12°C immediately after harvest. Transported in refrigerated containers at 10–12°C.',
    'France, Spain, UK, Netherlands, Germany, Nordic countries, Middle East (Gulf states).',
    'Hand-harvested in the early morning. Field heat removed within 1 hour. Sugar content tested before packing (Brix >10°).',
    'GlobalGAP, ISO 22000, BRC, IFS. Traceability from field to shipment.',
    'Moroccan Watermelon: Season, Varieties & Packaging Guide',
    'Sweet Moroccan watermelons exported with cold chain protection. Season, varieties, packaging and shipping details.',
    'published',
    '2026-05-05 08:00:00',
    '2026-05-05 08:00:00'
),

(
    'peppers',
    'Peppers from Morocco',
    'Colorful Moroccan peppers, from greenhouse to European markets in 5–7 days.',
    'Morocco — Tadla-Azilal and Meknes-Saïss regions. Greenhouse cultivation for consistent quality.',
    'Red bell peppers (California Wonder, California Wonder 301) | Yellow peppers (loco) | Orange peppers (California Wonder)',
    'October to June. Peak: November–April.',
    'Medium: 3–5 fruits per kg | Large: 2–3 fruits per kg | Extra-large: 1–2 fruits per kg',
    '500g clamshells (4 per master case) | 1kg flow-wrap punnets (2 per master case) | Loose 5kg cartons | 10kg bulk bins',
    'Regular weekly shipments from October to June, with increased volumes during peak season (December–March).',
    'Pre-cooled to 8–10°C. Shipped in refrigerated containers at 8–10°C with humidity control (80–90%).',
    'France, Spain, Germany, Netherlands, UK, Italy, Belgium, Nordic countries.',
    'Harvested at full color maturity. Sorted by size and color. Wax-coated for extended shelf life (7+ days at 10°C).',
    'GlobalGAP, ISO 22000, BRC, IFS. IPM with beneficial insects (no chemical pesticides).',
    'Moroccan Peppers: Varieties, Season & Export Guide',
    'Colorful Moroccan peppers packed and exported to Europe. Varieties, harvest season, packaging and cold chain.',
    'published',
    '2026-05-12 08:00:00',
    '2026-05-12 08:00:00'
),

(
    'courgettes',
    'Courgettes from Morocco',
    'Fresh Moroccan zucchinis, cultivated year-round in controlled greenhouse conditions.',
    'Morocco — Souss-Massa and Meknes-Saïss regions. Greenhouse-grown for consistent supply.',
    'Green zucchini (Black Beauty) | Yellow crookneck (Sunburst) | Patty pan (Sunburst) | Round (Rugosa) ',
    'Year-round production. Peak season: October to May. Summer harvest is lighter due to heat.',
    'Small: 8–12cm (100–150g each) | Medium: 12–16cm (150–250g each) | Large: 16–20cm (250–350g each)',
    '1kg clamshells (6 per master case) | 500g flow-wrap punnets | 5kg loose cartons with internal foam | 10kg bulk bins',
    'Daily harvesting possible (greenhouse). Shipments available 6 days per week.',
    'Pre-cooled to 8–10°C. Transported in perforated refrigerated containers with humidity control (85–90%).',
    'France, Spain, Netherlands, Germany, UK, Middle East (UAE, Qatar).',
    'Hand-harvested daily. Washed and sorted by size and color. Skin integrity checked to prevent bruising.',
    'GlobalGAP, ISO 22000, BRC. IPM-certified greenhouses with biological pest control.',
    'Moroccan Courgettes/Zucchini: Year-Round Supply Guide',
    'Fresh Moroccan zucchinis and courgettes, greenhouse-grown for year-round supply to European markets.',
    'published',
    '2026-05-14 08:00:00',
    '2026-05-14 08:00:00'
),

(
    'berries',
    'Berries from Morocco',
    'Moroccan strawberries, raspberries and blueberries — delicate, hand-picked and cold-chain shipped.',
    'Morocco — Tadla-Azilal and Meknes-Saïss regions. Greenhouse-grown for consistent supply.',
    'Strawberries (Albion, Mara des Bois, Ventana) | Raspberries (Tulameen, Killarney) | Blueberries (Ardi, Legacy, Duke)',
    'Strawberries: November to April (peak December–March). Raspberries: January to April. Blueberries: December to May.',
    'Strawberries: 15–25mm diameter (small), 25–35mm (medium), 35–45mm (large) |
Raspberries: 3–6mm (small), 6–9mm (medium), 9–12mm (large) |
Blueberries: 8–10mm (small), 10–12mm (medium), 12–14mm (large)',
    'Clamshells 125g (24 per master case) | Clamshells 250g (12 per master case) | Punnets 125g (24 per master case) | Bulk 1kg tubs',
    'Weekly shipments of strawberries (Nov–Apr), raspberries (Jan–Mar), blueberries (Dec–May).',
    'Flown to Paris (CDG) or shipped by sea in temperature-controlled containers at 0–2°C with shock-absorbing packaging.',
    'France, Spain, Netherlands, Germany, UK, Belgium, Luxembourg, Nordic countries, Gulf states (UAE, Saudi Arabia).',
    'Harvested at full ripeness. Packed in clean-room conditions. Metal detection and weight sorting before dispatch.',
    'GlobalGAP, ISO 22000, BRC, IFS. EU organic (on request for specific lots). Traceability via QR code on each clamshell.',
    'Moroccan Berries: Strawberries, Raspberries & Blueberries',
    'Delicate Moroccan berries, hand-picked and cold-chain shipped. Varieties, season, packaging and export markets.',
    'published',
    '2026-05-20 08:00:00',
    '2026-05-20 08:00:00'
),

(
    'dried-fruits',
    'Dried Fruits from Morocco',
    'Premium Moroccan dried fruits — apricots, raisins, dates and almonds — for snacks and ingredients.',
    'Morocco — Souss-Massa (apricots), Meknes-Saïss (raisins, dates), Tadla-Azilal (almonds).',
    'Apricots (delMonte, royal) | Raisins (sultana, flame, crimson) | Dates (medjool, deglet nour) | Almonds (nonpareil, mission)',
    'Year-round availability. Apricots and almonds: spring–fall harvest. Dates: October–December. Raisins: August–October.',
    'Apricots: 5–8mm pieces (small), 8–12mm (medium), 12–18mm (large) |
Raisins: seedless sultana, flame, or crimson |
Dates: 12–16mm (medium), 16–20mm (large) |
Almonds: whole, sliced, slivered, meal',
    '250g retail pouches | 500g standup pouches | 1kg bulk bags | 10kg master cases | 15kg bulk bins for ingredients',
    'Continuous stock available. Bulk quantities (1+ metric tons) upon request.',
    'Stored in climate-controlled warehouses (18–22°C, 50–55% RH). Shipped at ambient temperature in moisture-barrier packaging.',
    'France, Germany, Netherlands, UK, Belgium, Scandinavia, Middle East, Asia (Japan, South Korea), North America.',
    'All fruits are sun-dried or mechanically dehumidified. Sifted, sorted, and metal-detected. Moisture content tested (<15%).',
    'GlobalGAP, ISO 22000, BRC, IFS. HACCP-certified facilities. Kosher and halal certifications available on request.',
    'Moroccan Dried Fruits: Apricots, Raisins, Dates & Almonds',
    'Premium Moroccan dried fruits for snacks and ingredients. Varieties, packaging, availability and export markets.',
    'published',
    '2026-05-16 08:00:00',
    '2026-05-16 08:00:00'
),

(
    'figs',
    'Figs from Morocco',
    'Sweet Moroccan figs, hand-picked at peak ripeness for export to European markets.',
    'Morocco — Tadla-Azilal and Meknes-Saïss regions. Traditional orchards with drip irrigation.',
    'Fresh figs: Moroccan Beldia (brown-skin, pink flesh) | Dried figs: Beldia, Nadoria (soft, caramel) | Split figs (dried, halved)',
    'Fresh figs: June to August. Dried figs: June–September (fresh drying). Available year-round (stored/dried).',
    'Fresh: 5–7 per kg (small), 3–5 per kg (medium), 1–3 per kg (large) |
Dried whole: 40–60 pieces per kg |
Dried pieces: 20–40g per 100g pack',
    'Clamshells 250g (8 per master case) | Trays 500g (4 per master case) | Loose 2kg punnets | Dried: 250g pouches (24 per master case) | 1kg retail tubs',
    'Fresh figs: June–August (seasonal). Dried: year-round from current season stock.',
    'Fresh figs: pre-cooled to 4–6°C, shipped in ventilated containers with ethylene absorption. Dried: ambient, moisture-barrier packaging.',
    'France, Spain, Netherlands, Germany, UK, Middle East (UAE, Saudi Arabia, Kuwait), North Africa (Tunisia, Algeria).',
    'Hand-picked in the early morning. Fresh figs are tree-ripened before harvest. No sulfites added to dried figs.',
    'GlobalGAP, ISO 22000, BRC. IPM-certified orchards. Traceability from tree to packhouse.',
    'Moroccan Figs: Fresh and Dried, Season & Export Guide',
    'Sweet Moroccan figs, fresh and dried, exported to Europe and the Middle East. Season, varieties, packaging and quality.',
    'published',
    '2026-05-18 08:00:00',
    '2026-05-18 08:00:00'
);
