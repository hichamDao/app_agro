/**
 * Articles de blog FoodMax Group : textes reecrits (version humaine).
 *
 * - Chaque article est rattache a son "slug" : en executant ce fichier, les
 *   articles existants sont MIS A JOUR (titre, resume, contenu...) et ceux qui
 *   manquent sont crees. Rien n'est supprime.
 * - Les textes restent generaux et factuels (saisons, culture, conservation).
 *   Aucune certification, aucun pays livre, aucun equipement, aucune region
 *   precise n'y est affirme : ajoutez ces informations vous-meme, uniquement
 *   quand elles sont vraies et que vous pouvez les prouver.
 * - Les liens internes (products/, contact/...) sont relatifs a la racine du
 *   site : le blog les corrige automatiquement a l'affichage.
 *
 * A executer APRES blog_posts.sql, avec phpMyAdmin (onglet SQL).
 * Sauvegardez la table avant :  CREATE TABLE blog_posts_backup AS SELECT * FROM blog_posts;
 */

SET NAMES utf8mb4;

-- Morocco Fresh Produce Exports: A Guide for International Buyers
INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'Morocco Fresh Produce Exports: A Guide for International Buyers',
    'morocco-fresh-produce-exports-guide-for-international-buyers',
    'What an international buyer should know before sourcing fresh fruit and vegetables from Morocco: how the seasons run, what to decide before a first order and how we like to start.',
    '<h2>Why so many buyers look at Morocco</h2>
<p>Morocco is not a big country, but it holds an unusual mix of climates: a mild Atlantic coast, a warm and bright south, mountains with cool nights, and wide inland plains. Together they keep the harvest going for much of the year, and that is the simple reason why a buyer in northern Europe can find Moroccan tomatoes, citrus and berries in the middle of winter.</p>
<p>It is also close. Europe is a matter of days away by truck and ferry, or by sea, which is a big advantage for products that do not like to wait.</p>

<h2>What a season looks like</h2>
<p>Every product has its own window, and the dates move a little each year with the weather. As a rough guide, this is how the main seasons usually run:</p>
<table>
<thead><tr><th>Product</th><th>Usual main season</th></tr></thead>
<tbody>
<tr><td><strong>Citrus</strong></td><td>Autumn to spring</td></tr>
<tr><td><strong>Tomatoes</strong></td><td>Autumn to late spring</td></tr>
<tr><td><strong>Berries</strong></td><td>Winter to spring</td></tr>
<tr><td><strong>Peppers</strong></td><td>Autumn to early summer</td></tr>
<tr><td><strong>Watermelon</strong></td><td>Spring to late summer</td></tr>
<tr><td><strong>Figs</strong></td><td>Late summer to autumn</td></tr>
</tbody>
</table>
<p>These are general windows, not promises. If you need a product on a particular date, just ask us what is available right now.</p>

<h2>What to decide before your first order</h2>
<p>You do not need to have everything worked out. But the more you can tell us, the quicker and the more precisely we can answer:</p>
<ul>
<li><strong>The product and variety</strong> you have in mind, and the size you prefer.</li>
<li><strong>The packing</strong>: loose cartons, trays, punnets, retail packs or private label.</li>
<li><strong>The quantity</strong> and how often you would like to receive it.</li>
<li><strong>The destination</strong>, so that we can think about labels and documents.</li>
<li><strong>How it should travel</strong>, by truck or by container, and how soon you need it.</li>
</ul>

<h2>How we like to start</h2>
<p>For a new customer, a trial shipment is usually a sensible way to begin. It lets you judge the quality, the packing and the way we work with real goods in your hands, before anything bigger is decided. We would much rather earn a second order than push for a large first one.</p>

<h2>Let us talk</h2>
<p>Have a look at <a href="products/">our products</a> to see what we work with, then tell us what you need. If you would like to talk about a first order, <a href="contact/">write to our team</a> and a real person will reply within one business day.</p>',
    'Morocco',
    'products/',
    'Our products',
    'blog-morocco.jpg',
    'Morocco Fresh Produce Exports: A Guide for Buyers',
    'How the seasons run in Morocco, what to decide before a first order, and how to start sourcing fresh fruit and vegetables from FoodMax Group.',
    'published',
    '2026-05-15 09:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `excerpt` = VALUES(`excerpt`),
    `content` = VALUES(`content`),
    `category` = VALUES(`category`),
    `product_link` = VALUES(`product_link`),
    `product_label` = VALUES(`product_label`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Moroccan Tomatoes: Varieties, Season and Export Information
INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'Moroccan Tomatoes: Varieties, Season and Export Information',
    'moroccan-tomatoes-varieties-season-export',
    'Round, cherry, plum and truss tomatoes: how they are grown in Morocco, when the season runs, and what to know about picking, packing and keeping them.',
    '<h2>The tomato types buyers ask about</h2>
<p>Tomatoes look simple, but a buyer can mean very different things by the word. These are the types we are asked about most often.</p>

<h3>Round tomatoes</h3>
<p>The everyday tomato for slicing and salads. Buyers usually specify a size range and a colour stage, and both matter more than people expect.</p>

<h3>Cherry tomatoes</h3>
<p>Small and sweet, often sold in punnets and baskets. They are picked in clusters or one by one, which makes the work slower and the price different.</p>

<h3>Plum tomatoes</h3>
<p>Longer, firmer and meatier, and a good choice for cooking and for processing.</p>

<h3>Truss tomatoes and specialty types</h3>
<p>Tomatoes sold on the vine, and coloured types for shops that want something a little different. Ask us what is available in the season you are interested in.</p>

<h2>How they are grown</h2>
<p>Tomatoes love warmth and light. A large share of Morocco''s winter and spring production is grown in greenhouses in the south of the country, where it stays mild when other places are cold, and the warmer months bring field-grown fruit. The plants grow tall along strings and the fruit is picked by hand, bunch after bunch, over many weeks.</p>

<h2>When the season runs</h2>
<p>The main season is generally from <strong>October to May</strong>, with the biggest volumes in the middle of winter. The exact dates change a little each year with the weather.</p>

<h2>Picking and keeping</h2>
<p>The colour of the fruit at harvest depends on the journey. Tomatoes that have to travel far are picked just as they begin to turn, so that they arrive at their best, while fruit for nearby markets can stay longer on the plant and be fuller in colour.</p>
<p>Once picked, tomatoes should be kept at around 10 to 12 degrees. A cold fridge is not a friend of the tomato: below about 10 degrees it loses flavour and firmness.</p>

<h2>Packing</h2>
<p>Tomatoes are usually packed loose in cartons, in trays, or in punnets and baskets for retail, depending on the type. We can discuss the format that suits your customers, including private label.</p>

<p>You can read our <a href="products/tomatoes/">buyer''s guide to tomatoes</a>, see <a href="products/7/Tomatoes/">the tomatoes in our catalogue</a>, or <a href="contact/">ask us for a quote</a>.</p>',
    'Tomatoes',
    'products/7/Tomatoes/',
    'Moroccan Tomatoes',
    'blog-tomatoes.jpg',
    'Moroccan Tomatoes: Types, Season and How They Travel',
    'Types of Moroccan tomatoes, the October to May season, how they are picked and packed, and how to keep them at their best. Ask FoodMax Group for a quote.',
    'published',
    '2026-05-10 14:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `excerpt` = VALUES(`excerpt`),
    `content` = VALUES(`content`),
    `category` = VALUES(`category`),
    `product_link` = VALUES(`product_link`),
    `product_label` = VALUES(`product_label`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Moroccan Citrus Fruits: Orange and Lemon Export Guide
INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'Moroccan Citrus Fruits: Orange and Lemon Export Guide',
    'moroccan-citrus-orange-lemon-export-guide',
    'Navel, blood and Valencia oranges, clementines and lemons: why Moroccan citrus tastes the way it does, how the season unfolds and how to keep it in good shape.',
    '<h2>Why Moroccan citrus tastes the way it does</h2>
<p>Citrus is one of the great strengths of Moroccan agriculture. The trees like mild winters and long bright summers, and the cool nights of autumn are what bring out the colour of the peel and the sweetness of the juice. A citrus fruit does not get sweeter once it has been picked, so the harvest is timed with care and done by hand, with clippers, so the peel is never torn.</p>

<h2>The fruit, one family at a time</h2>

<h3>Oranges</h3>
<ul>
<li><strong>Navel oranges</strong> are seedless and easy to peel, the classic orange for eating fresh. They are at their best fresh rather than as juice, because their juice turns bitter soon after pressing.</li>
<li><strong>Blood oranges</strong> have red flesh and a berry-like flavour, and they are a winter speciality.</li>
<li><strong>Valencia oranges</strong> are the late-season orange, very juicy and loved for juice as much as for eating. They stay on the tree for many months, so the peel can turn slightly green again in warm weather while the fruit inside is perfectly ripe.</li>
</ul>

<h3>Clementines and mandarins</h3>
<p>The easy-peel citrus, sweet and popular with families. They come early in the season, and the loose skin means they need gentle handling.</p>

<h3>Lemons</h3>
<p>Lemons are harvested over a long period of the year, with the best supply usually in the cooler months. They like a little more warmth than other citrus and do not enjoy cold storage.</p>

<h2>How the season unfolds</h2>
<table>
<thead><tr><th>Fruit</th><th>Usual period</th></tr></thead>
<tbody>
<tr><td>Clementines and mandarins</td><td>Autumn to winter</td></tr>
<tr><td>Navel oranges</td><td>Late autumn to spring</td></tr>
<tr><td>Blood oranges</td><td>Winter</td></tr>
<tr><td>Valencia oranges</td><td>Spring into early summer</td></tr>
<tr><td>Lemons</td><td>Much of the year, best in the cooler months</td></tr>
</tbody>
</table>
<p>Dates vary with the weather each year, so ask us what is available when you are ready to order.</p>

<h2>Packing and keeping</h2>
<p>Citrus is a hardy fruit and travels well when it is kept cool and the air can move around it, which is why ventilated packing matters. We can talk about mesh bags, cartons and trays, retail packs, or bulk for processing and foodservice, whatever fits the way you sell.</p>

<p>Read our guides to <a href="products/oranges/">oranges</a> and <a href="products/lemons/">lemons</a>, browse <a href="products/3/Citrus/">the citrus in our catalogue</a>, or <a href="contact/">send us your request</a>.</p>',
    'Citrus',
    'products/3/Citrus/',
    'Moroccan Citrus',
    'blog-citrus.jpg',
    'Moroccan Citrus: Oranges, Lemons and the Citrus Season',
    'Navel, blood and Valencia oranges, clementines and lemons from Morocco: how the season unfolds, how citrus is picked and how to keep it. Ask us for a quote.',
    'published',
    '2026-05-08 10:30:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `excerpt` = VALUES(`excerpt`),
    `content` = VALUES(`content`),
    `category` = VALUES(`category`),
    `product_link` = VALUES(`product_link`),
    `product_label` = VALUES(`product_label`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Moroccan Watermelon Export: Season, Varieties and Packaging
INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'Moroccan Watermelon Export: Season, Varieties and Packaging',
    'moroccan-watermelon-export-season-varieties-packaging',
    'Seeded, seedless and mini watermelons from Morocco: when the season runs, how growers know a watermelon is ready, and why loading matters as much as temperature.',
    '<h2>A fruit that is mostly water, and all about timing</h2>
<p>Watermelon is mostly water, which is exactly why it is so refreshing on a hot day. It is also a fruit that does not forgive a wrong harvest: once it is cut from the vine it will not ripen any further, so the whole quality of the fruit depends on the moment it was picked.</p>

<h2>How growers know a watermelon is ready</h2>
<p>There is no single sign, so experienced pickers look at several things together: the pale patch on the underside where the fruit rested on the ground, the dried tendril close to the stem, and the hollow sound when the fruit is tapped. Some buyers also ask for a minimum sugar level, which we can discuss.</p>

<h2>The types</h2>
<ul>
<li><strong>Seeded watermelon</strong>, the large traditional fruit, often striped or dark green.</li>
<li><strong>Seedless watermelon</strong>, very popular in supermarkets because it is easy to eat.</li>
<li><strong>Mini watermelon</strong>, a small fruit for one or two people, handy for retail.</li>
<li><strong>Yellow-fleshed types</strong>, a specialty for buyers who want something different.</li>
</ul>
<p>Ask us which of these are available in the period you are interested in.</p>

<h2>When the season runs</h2>
<p>Watermelon is a warm-season crop. Early fruit comes from the warmest areas in spring, and the main season runs through the summer, generally between <strong>March and August</strong>.</p>

<h2>Packing and transport</h2>
<p>A watermelon is heavy and bulky, so loading matters as much as temperature. Fruit needs to be well supported on the pallet, with nothing pressing on it, to avoid cracks and bruises. As for temperature, around 10 to 15 degrees is a good range, because watermelon can be damaged by storage that is too cold.</p>
<p>Formats range from loose fruit on pallets and bulk bins to cartons for mini watermelons, and individual labelling for retail can be discussed.</p>

<p>Read our <a href="products/watermelon/">buyer''s guide to watermelon</a>, see <a href="products/">our products</a>, or <a href="contact/">write to us</a> for availability.</p>',
    'Watermelon',
    'products/',
    'Moroccan Watermelon',
    'blog-watermelon.jpg',
    'Moroccan Watermelon: Types, Season and Transport',
    'Seeded, seedless and mini watermelons from Morocco: season from spring to summer, how ripeness is judged, and how they are packed and transported.',
    'published',
    '2026-05-05 11:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `excerpt` = VALUES(`excerpt`),
    `content` = VALUES(`content`),
    `category` = VALUES(`category`),
    `product_link` = VALUES(`product_link`),
    `product_label` = VALUES(`product_label`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- How Fresh Produce Is Exported from Morocco to Europe
INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'How Fresh Produce Is Exported from Morocco to Europe',
    'how-fresh-produce-is-exported-from-morocco-to-europe',
    'A plain walk through the journey of a shipment, from the field to your warehouse: what happens at each step, and where things can go wrong if nobody is paying attention.',
    '<h2>From the field to your warehouse</h2>
<p>People often imagine that exporting fresh produce is mostly paperwork and trucks. In reality it is a chain of small steps, and each step either protects the quality of the fruit or quietly takes a little of it away. Here is how a shipment generally goes, and what we think about at each stage.</p>

<h3>1. Harvest</h3>
<p>Produce is picked at the right stage of maturity, and as early in the day as possible, while it is still cool. Heat is the enemy from the very first minute, so getting the fruit out of the sun matters.</p>

<h3>2. Cooling</h3>
<p>The sooner the field heat is taken out of the produce, the longer it will last. How this is done depends on the product: dense fruit and delicate berries or leaves are not cooled in the same way.</p>

<h3>3. Sorting and grading</h3>
<p>Fruit is sorted by size, colour and condition against the specification you gave us. Anything that does not match is set aside, rather than slipped into the order.</p>

<h3>4. Packing and labelling</h3>
<p>Produce is packed in the format you asked for, whether loose cartons, trays, punnets, retail packs or private label, with the labels and information your market requires.</p>

<h3>5. Cold storage and loading</h3>
<p>Packed goods wait in the cold until the truck or container is ready, and the vehicle itself should already be at the right temperature before loading starts, so that the load does not warm up on the way in.</p>

<h3>6. Transport</h3>
<p>To Europe there are two usual routes: trucks, which cross the Strait by ferry, and containers by sea. Which one is better depends on the product, the distance and how soon you need the goods. Fast and delicate products tend to prefer the road, heavier and more resistant ones are comfortable by sea.</p>

<h2>What to check at each stage</h2>
<table>
<thead><tr><th>Stage</th><th>What matters</th></tr></thead>
<tbody>
<tr><td>Harvest</td><td>Maturity, cleanliness, time in the sun</td></tr>
<tr><td>Packing</td><td>Size, weight, labels that match the specification</td></tr>
<tr><td>Cold storage</td><td>The temperature is the right one for the product</td></tr>
<tr><td>Loading</td><td>The vehicle is clean, cool and the load is well supported</td></tr>
<tr><td>Arrival</td><td>Condition of the goods and temperature records, if you asked for them</td></tr>
</tbody>
</table>

<h2>Documents</h2>
<p>A shipment travels with its paperwork, usually at least a commercial invoice and a packing list, and, depending on the product and the destination, certificates such as a phytosanitary certificate or other documents your country asks for. Tell us where the goods are going and we will explain what is needed.</p>

<p>Questions about a shipment of your own? <a href="contact/">Request a quote</a> and we will go through it with you.</p>',
    'Logistics',
    '',
    NULL,
    'blog-logistics.jpg',
    'How Fresh Produce Is Exported from Morocco to Europe',
    'The journey of a fresh produce shipment from a Moroccan farm to a European warehouse, step by step, and what to check at each stage.',
    'published',
    '2026-05-01 09:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `excerpt` = VALUES(`excerpt`),
    `content` = VALUES(`content`),
    `category` = VALUES(`category`),
    `product_link` = VALUES(`product_link`),
    `product_label` = VALUES(`product_label`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);

-- Why Cold Chain Is Important for Fresh Produce Exports
INSERT INTO `blog_posts`
    (`title`, `slug`, `excerpt`, `content`, `category`, `product_link`, `product_label`, `image`, `meta_title`, `meta_description`, `status`, `created_at`, `updated_at`)
VALUES (
    'Why Cold Chain Is Important for Fresh Produce Exports',
    'why-cold-chain-is-important-for-fresh-produce-exports',
    'Why temperature decides how long fresh fruit and vegetables last, why colder is not always better, and what a broken cold chain really costs.',
    '<h2>What the cold chain is</h2>
<p>The cold chain is the unbroken line of controlled temperature that follows a product from the moment it is picked until it reaches the person who will eat it. Fresh fruit and vegetables are alive, they keep breathing after harvest, and the warmer they are, the faster they age.</p>

<h2>Why temperature matters so much</h2>
<p>As a rule of thumb, produce deteriorates two to three times faster for every 10 degrees of extra warmth. That is why a few hours in the sun on a loading dock can cost more shelf life than many days in a well-kept cold room.</p>
<p>A broken cold chain is not only a question of quality. Warmth also helps microorganisms to multiply, so keeping the temperature under control is part of food safety.</p>

<h2>Colder is not always better</h2>
<p>This surprises a lot of people. Many products come from warm climates and are injured by cold, a problem called chilling injury. A tomato kept too cold loses flavour and firmness, a courgette develops pits on its skin, and a watermelon softens. Each product has its own comfortable range, and the job is to find it, not simply to make everything as cold as possible.</p>

<h2>A rough guide by product</h2>
<table>
<thead><tr><th>Product</th><th>Comfortable range (approximate)</th></tr></thead>
<tbody>
<tr><td>Berries</td><td>Close to freezing, around 0 degrees</td></tr>
<tr><td>Leafy greens</td><td>Just above freezing, around 0 to 2 degrees</td></tr>
<tr><td>Peppers and courgettes</td><td>Around 7 to 10 degrees</td></tr>
<tr><td>Tomatoes and eggplants</td><td>Around 10 to 12 degrees</td></tr>
<tr><td>Watermelon</td><td>Around 10 to 15 degrees</td></tr>
<tr><td>Citrus</td><td>Cool; lemons like it a little warmer than oranges</td></tr>
</tbody>
</table>
<p>These are general figures. The right temperature also depends on the variety and on how ripe the fruit is, so we always prefer to talk about the product you have in mind.</p>

<h2>Keeping track</h2>
<p>Many buyers like to receive the temperature records of a shipment, so that if something looks wrong on arrival there is a clear answer about what happened. If this matters to you, tell us when you ask for your quote and we will tell you what is possible.</p>

<p>Want to talk about how your own goods should travel? <a href="contact/">Ask us</a>.</p>',
    'Cold chain',
    '',
    NULL,
    'blog-cold-chain.jpg',
    'Why the Cold Chain Matters for Fresh Produce Exports',
    'Why temperature decides how long fresh produce lasts, why colder is not always better, and what a broken cold chain costs buyers and customers.',
    'published',
    '2026-04-28 14:00:00',
    NOW()
)
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `excerpt` = VALUES(`excerpt`),
    `content` = VALUES(`content`),
    `category` = VALUES(`category`),
    `product_link` = VALUES(`product_link`),
    `product_label` = VALUES(`product_label`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `status` = VALUES(`status`);
