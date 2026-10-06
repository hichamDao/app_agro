/**
 * Exemples d'articles de blog avec des donnees reelles pour Foodmax Group.
 *
 * Chaque article est relie a une categorie de produit et contient des
 * informations exportables (saisonnalite, varietes, conditionnement, etc.)
 * que les acheteurs internationaux cherchent couramment.
 *
 * Executer APRES avoir cree la table blog_posts (voir blog_posts.sql).
 */

INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES

(
    'Morocco Fresh Produce Exports: A Guide for International Buyers',
    'morocco-fresh-produce-exports-guide-for-international-buyers',
    'From our Marrakech office to your warehouse: everything an international buyer needs to know about sourcing fresh fruits and vegetables from Morocco.',
    '<h2>Why Morocco for fresh produce?</h2>\n<p>Morocco straddles two climates — the cool, wet Atlantic coast and the warm, arid interior — which means we can offer a long harvest calendar without gaps. From November through May, our greenhouses and open-field farms produce citrus, berries, tomatoes, and watermelon to exacting international standards.</p>\n\n<h3>Climate and seasons at a glance</h3>\n<table>\n<tr><td><strong>Citrus</strong></td><td>October – May</td></tr>\n<tr><td><strong>Tomatoes</strong></td><td>October – May</td></tr>\n<tr><td><strong>Strawberries</strong></td><td>November – April</td></tr>\n<tr><td><strong>Watermelon</strong></td><td>March – August</td></tr>\n<tr><td><strong>Peppers</strong></td><td>October – June</td></tr>\n</table>\n\n<h2>Certifications and standards</h2>\n<p>All our partner growers hold GlobalGAP certification and comply with EU phytosanitary requirements. We provide: GlobalGAP, ISO 22000, and on request, organic (EU regulation 2018/848) certification for specific lots.</p>\n\n<h2>How to place your first order</h2>\n<p>We suggest starting with a trial shipment of one container to evaluate quality and logistics. Contact our team with your preferred product, volume, and destination, and we will reply within one business day with availability and pricing.</p>\n<p><strong>Ready to start sourcing?</strong> <a href="products/">See our full catalogue</a> or <a href="contact/">contact our team</a>.</p>',
    'Morocco',
    'products/3/Citrus/',
    'Moroccan Citrus',
    'blog-morocco.jpg',
    'Morocco Fresh Produce Exports: A Guide for International Buyers',
    'From our Marrakech office to your warehouse: everything an international buyer needs to know about sourcing fresh fruits and vegetables from Morocco.',
    'published',
    '2026-05-15 09:00:00',
    '2026-05-15 09:00:00'
),

(
    'Moroccan Tomatoes: Varieties, Season and Export Information',
    'moroccan-tomatoes-varieties-season-export',
    'Discover the tomato varieties we grow in Morocco, their harvest season from October to May, and how we pack and export them.',
    '<h2>Our tomato varieties</h2>\n<p>We grow three main tomato types suited to export:</p>\n\n<h3>Cherry tomatoes</h3>\n<p>Sweet, bite-sized fruit in red, yellow, and striped varieties. Harvested by hand in the early morning and packed within two hours to preserve firmness.</p>\n\n<h3>Round tomatoes (Roma)</h3>\n<p>The classic plum tomato, ideal for processing and salads. Available in 200g–300g per fruit.</p>\n\n<h3>Colored tomatoes (Kumato, Bamano)</h3>\n<p>Purple-brown Kumato and yellow Bamano varieties, grown in greenhouse for premium markets.</p>\n\n<h2>Season and availability</h2>\n<p>Our main harvest runs from <strong>October to May</strong>. Peak volumes are reached in January–March.</p>\n\n<h2>Packaging options</h2>\n<table>\n<tr><td><strong>Shaker 250g</strong></td><td>10×250g bottles per tray</td></tr>\n<tr><td><strong>Bucket 500g</strong></td><td>12×500g punnets per crate</td></tr>\n<tr><td><strong>Loose 4kg</strong></td><td>Carton with internal foam trays</td></tr>\n<tr><td><strong>Tray 250g</strong></td><td>15×250g on floret trays</td></tr>\n</table>\n\n<h3>Cold chain</h3>\n<p>All tomato shipments are kept at 10–12°C throughout transport.</p>\n\n<h2>Export markets</h2>\n<p>Currently shipping weekly to France, Spain, Germany, and the Netherlands. We can adapt packaging and labeling to meet specific retailer requirements.\p>\n<p><a class="btn" href="products/7/Tomatoes/">Discover our Tomato products</a></p>',
    'Tomatoes',
    'products/7/Tomatoes/',
    'Moroccan Tomatoes',
    'blog-tomatoes.jpg',
    'Moroccan Tomatoes: Varieties, Season and Export Information',
    'Tomato varieties, harvest season and export packaging from Morocco.',
    'published',
    '2026-05-10 14:00:00',
    '2026-05-10 14:00:00'
),

(
    'Moroccan Citrus Fruits: Orange and Lemon Export Guide',
    'moroccan-citrus-orange-lemon-export-guide',
    'A complete guide to Moroccan oranges and lemons: varieties, harvest calendar, packaging and export standards.',
    '<h2>Why Moroccan citrus?</h2>\n<p>Morocco produces some of the world''s finest citrus thanks to our Mediterranean climate, mineral-rich soil, and decades of growing expertise. Our orchards are located in the Saïss and Tadla regions, where cool nights and sunny days produce intensely flavored fruit.</p>\n\n<h2>Varieties we export</h2>\n<h3>Oranges</h3>\n<ul>\n<li><strong>Blood oranges (Moro, Tarocco)</strong> — November to February</li>\n<li><strong>Navel oranges (Washington Navel, Late Newham)</strong> — December to April</li>\n<li><strong>Esla oranges</strong> — early season, October–November</li>\n</ul>\n\n<h3>Lemons</h3>\n<ul>\n<li><strong>Eureka</strong> — year-round but peaks November–April</li>\li>\n<li><strong>Eurogold</strong> — seedless, high yield, October–March</li>\n</ul>\n\n<h2>Harvest calendar</h2>\n<table>\n<tr><th>Variety</th><th>Peak season</th></tr>\n<tr><td>Blood oranges</td><td>November – January</td></tr>\n<tr><td>Navel oranges</td><td>December – March</td></tr>\n<tr><td>Eureka lemons</td><td>November – April</td></tr>\n<tr><td>Eurogold lemons</td><td>October – March</td></tr>\n</table>\n\n<h2>Packaging and export standards</h2>\n<p>Standard exports use 8 kg mesh bags or 4 kg cardboard trays. For retail markets, we offer:</p>\n<ul>\n<li>1.5kg clamshells (4 per master case)</li>\n<li>1kg flow-wrap punnets (6 per master case)</li>\n<li>Loose bulk in ventilated bins for foodservice</li>\n</ul>\n<p>All packaging is designed for a 2,000+ mile journey to European markets.</p>\n\n<h3>Quality and certifications</h3>\n<p>Our citrus is GlobalGAP certified, with regular BRC and IFS audits. Pheromone traps monitor pest pressure, keeping chemical treatments minimal.</p>\n\n<p><a class="btn" href="products/3/Citrus/">Discover our Citrus products</a></p>',
    'Citrus',
    'products/3/Citrus/',
    'Moroccan Citrus',
    'blog-citrus.jpg',
    'Moroccan Citrus Fruits: Orange and Lemon Export Guide',
    'Orange and lemon varieties, harvest calendar and export packaging from Morocco.',
    'published',
    '2026-05-08 10:30:00',
    '2026-05-08 10:30:00'
),

(
    'Moroccan Watermelon Export: Season, Varieties and Packaging',
    'moroccan-watermelon-export-season-varieties-packaging',
    'Watermelon export from Morocco: peak season March to August, seedless varieties, and how we keep them fresh during transit.',
    '<h2>Watermelon season in Morocco</h2>\n<p>The Moroccan watermelon harvest runs from <strong>March to August</strong>, with peak volumes in May–June. We grow both seeded and seedless varieties suited to export.</p>\n\n<h2>Varieties we offer</h2>\n<table>\n<tr><th>Variety</th><th>Type</th><th>Weight range</th><th>Season</th></tr>\n<tr><td>Crimson Sweet</td><td>Seeded</td><td>6–9 kg</td><td>March – May</td></tr>\n<tr><td>Yellow Crimson</td><td>Seeded</td><td>5–8 kg</td><td>April – June</td></tr>\n<tr><td>Mini Love</td><td>Seedless</td><td>3–5 kg</td><td>May – July</td></tr>\n<tr><td>Sugar Tripplesweet</td><td>Seedless</td><td>7–11 kg</td><td>June – August</td></tr>\n</table>\n\n<h2>Packaging solutions</h2>\n<p>Watermelons are individually sleeved in ventilated clamshells to prevent bruising, then packed in 3-pallet-high loads for efficient container filling. We use:</p>\n<ul>\n<li>Individual poly sleeves for retail</li>\n<li>Half-trays of 6–8 fruits per master case</li>\n<li>Bulk bins for wholesale (12 kg each)</li>\n</ul>\n\n<h2>Cold chain during export</h3>\n<p>Watermelons travel at 10–12°C in refrigerated 40'' containers. The entire journey from farm to European warehouse takes 5–7 days, with continuous temperature monitoring.</p>\n\n<h2>Export markets</h2>\n<p>We currently export to France, Spain, the UK, and the Netherlands. Early-season fruit goes to the UK; peak-season volumes target the Gulf markets.</p>\n\n<p><a class="btn" href="products/">See all our products</a></p>',
    'Watermelon',
    '',
    'Moroccan Watermelon',
    'blog-watermelon.jpg',
    'Moroccan Watermelon Export: Season, Varieties and Packaging',
    'Watermelon varieties, harvest season and export packaging from Morocco.',
    'published',
    '2026-05-05 11:00:00',
    '2026-05-05 11:00:00'
),

(
    'How Fresh Produce Is Exported from Morocco to Europe',
    'how-fresh-produce-is-exported-from-morocco-to-europe',
    'A behind-the-scenes look at our export process: from harvest in the field to delivery at European warehouses.',
    '<h2>From farm to warehouse: the export journey</h2>\n<p>Getting fresh produce from a Moroccan farm to a European warehouse involves five critical steps, each with its own quality checkpoints.</p>\n\n<h3>Step 1: Harvest and pre-cooling</h3>\n<p>Produce is harvested early in the morning to minimize heat exposure. Within two hours, it enters our hydrocoolers set to 2–4°C below ambient temperature. This “crash-cooling” stops respiration and preserves firmness.</p>\n\n<h3>Step 2: Sorting and grading</h3>\n<p>At our packing house, produce passes through optical sorters that remove misshapen or diseased items. A second manual pass catches anything the machines miss. Final grade is determined by size, color, and weight against the buyer’s specification.</p>\n\n<h3>Step 3: Packing and labeling</h3>\n<p>Produce is packed into the customer’s requested format — clamshells, punnets, bags, or bulk bins. Custom labels with PLU codes, origin, and best-before dates are applied. Each master case is weighed and its contents verified against the shipping manifest.</p>\n\n<h3>Step 4: Cold storage and staging</h3>\n<p>Packed produce is moved to cold storage at 0–2°C (for leafy greens) to 10–12°C (for tomatoes, citrus). Shipments are staged by destination: trucks for Spain/Portugal (24–48h transit), container ships for Northern Europe (7–10 days).</p>\n\n<h3>Step 5: Container loading and transport</h3>\n<p>Refrigerated 40" high-cube containers are pre-cooled to 2°C, then loaded with palletized produce. Temperature sensors log every hour. The container is sealed and shipped to the destination port, where it is delivered directly to the buyer’s warehouse.</p>\n\n<h2>Quality control checkpoints</h2>\n<table>\n<tr><th>Stage</th><th>Checkpoint</th></tr>\n<tr><td>Harvest</td><td>Temperature and Brix measurement</td></tr>\n<tr><td>Packing</td><td>Weight, dimension, label accuracy</td></tr>\n<tr><td>Cold storage</td><td>Temperature log verification</td></tr>\n<tr><td>Container</td><td>Seal integrity, sensor calibration</td></tr>\n<tr><td>Delivered</td><td>Signature and temperature log handover</td></tr>\n</table>\n\n<h2>Documentation we provide</h2>\n<p>Every shipment includes: commercial invoice, packing list, phytosanitary certificate, GlobalGAP certificate, and the customer’s required retailer forms. All documents are provided digitally 24h before arrival and in printed form inside the container.</p>\n\n<p><a class="btn" href="contact/">Request a quote</a></p>',
    'Logistics',
    '',
    NULL,
    'blog-logistics.jpg',
    'How Fresh Produce Is Exported from Morocco to Europe',
    '',
    'published',
    '2026-05-01 09:00:00',
    '2026-05-01 09:00:00'
),

(
    'Why Cold Chain Is Important for Fresh Produce Exports',
    'why-cold-chain-is-important-for-fresh-produce-exports',
    'Understanding cold chain management: why temperature control from farm to shelf is critical for quality, shelf life and food safety.',
    '<h2>What is the cold chain?</h2>\n<p>The cold chain is an uninterrupted temperature-controlled supply line — from the moment produce leaves the farm until it arrives at the consumer’s refrigerator. For fresh fruits and vegetables, even brief temperature excursions can accelerate decay and increase the risk of microbial growth.</p>\n\n<h2>Why temperature matters</h2>\n<p>Every 1°C increase above optimal storage temperature can <strong>halve the shelf life</strong> of most produce. For a 14-day container journey:</p>\n<ul>\n<li>At 2°C: shelf life extended to 28+ days</li>\n<li>At 8°C: shelf life reduced to 7–10 days</li>\n<li>At 15°C: spoilage in 2–3 days</li>\n</ul>\n\n<h2>Our cold chain standards</h3>\n<h3>Pre-cooling (0–4°C)</h3>\n<p>Immediately after harvest, produce is cooled to remove field heat. We use hydrocooling for dense items (tomatoes, peppers) and forced-air cooling for delicate ones (berries, leaves). Target: <10°C above ambient within 2 hours.</p>\n\n<h3>Transport (0–12°C)</h3>\n<p>All shipments use <strong>ATP-certified refrigerated 40" high-cube containers</strong> with:</p>\n<ul>\n<li>Digital temperature recorders logging every 15 minutes</li>\n<li>Solar-powered container seals for tamper evidence</li>\n<li>Backup generators for power outages</li>\n<li>Real-time GPS + temperature monitoring via IoT sensors</li>\n</ul>\n\n<h3>Delivery (same as storage)</h3>\n<p>At destination, containers are moved directly to cold storage or the buyer’s warehouse. Temperature logs are handed over with the delivery receipt for traceability.</p>\n\n<h2>Temperature guidelines by product</h2>\n<table>\n<tr><th>Product</th><th>Optimal range</th><th>Notes</th></tr>\n<tr><td>Citrus (oranges, lemons)</td><td>2–4°C</td><td>Can handle slight cold; avoid freezing</td></tr>\n<tr><td>Tomatoes</td><td>10–12°C</td><td>Cold damage below 10°C; never refrigerate</td></tr>\n<tr><td>Watermelon</td><td>10–12°C</td><td>Heat accelerates softening and sugar loss</td></tr>\n<tr><td>Leafy greens</td><td>0–2°C</td>\td>High respiration; shortest shelf life</td></tr>\n<tr><td>Berries</td><td>0–2°C</td>\td>Must arrive dry; moisture causes mold</td></tr>\n</table>\n\n<h2>Food safety implications</h2>\n<p>Maintaining the cold chain prevents the growth of Salmonella, E. coli, and Listeria, which double in population every 20 minutes at room temperature. A broken cold chain isn’t just a quality issue — it’s a food safety risk.</p>\n\n<h3>Traceability and reporting</h3>\n<p>Each container’s temperature log is archived for 24 months and cross-referenced with delivery records. If a temperature excursion occurs, we can isolate affected pallets within 2 hours of notification.</p>\n\n<p><a class="btn" href="contact/">Ask about our cold chain guarantees</a></p>',
    'Cold chain',
    '',
    NULL,
    'blog-cold-chain.jpg',
    'Why Cold Chain Is Important for Fresh Produce Exports',
    '',
    'published',
    '2026-04-28 14:00:00',
    '2026-04-28 14:00:00'
);
