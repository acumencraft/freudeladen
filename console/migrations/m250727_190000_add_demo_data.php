<?php

use yii\db\Migration;

/**
 * Handles adding demo data for FREUDELADEN.DE
 */
class m250727_190000_add_demo_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add demo categories
        $this->batchInsert('{{%categories}}', ['name', 'slug', 'description', 'status'], [
            ['Elektronik', 'elektronik', 'Moderne Elektronikprodukte für den Alltag', 1],
            ['Mode', 'mode', 'Stilvolle Kleidung und Accessoires', 1],
            ['Haushalt', 'haushalt', 'Praktische Haushaltsartikel', 1],
            ['Sport', 'sport', 'Sportartikel und Fitnessgeräte', 1],
            ['Bücher', 'buecher', 'Interessante Bücher und Literatur', 1],
        ]);

        // Get category IDs
        $elektronikId = $this->db->createCommand('SELECT id FROM {{%categories}} WHERE slug = "elektronik"')->queryScalar();
        $modeId = $this->db->createCommand('SELECT id FROM {{%categories}} WHERE slug = "mode"')->queryScalar();
        $haushaltId = $this->db->createCommand('SELECT id FROM {{%categories}} WHERE slug = "haushalt"')->queryScalar();
        $sportId = $this->db->createCommand('SELECT id FROM {{%categories}} WHERE slug = "sport"')->queryScalar();
        $buecherId = $this->db->createCommand('SELECT id FROM {{%categories}} WHERE slug = "buecher"')->queryScalar();

        // Add demo products
        $this->batchInsert('{{%products}}', ['category_id', 'name', 'slug', 'description', 'short_description', 'price', 'sale_price', 'stock', 'status'], [
            [$elektronikId, 'Smartphone X1', 'smartphone-x1', 'Modernster Smartphone mit allem was Sie brauchen. Hochwertige Kamera, schneller Prozessor und langlebiger Akku.', 'Premium Smartphone mit Top-Ausstattung', 599.99, 499.99, 10, 1],
            [$elektronikId, 'Laptop Pro 15', 'laptop-pro-15', 'Professioneller Laptop für Arbeit und Entertainment. Intel i7 Prozessor, 16GB RAM, 512GB SSD.', '15 Zoll Laptop für Profis', 1299.99, null, 5, 1],
            [$elektronikId, 'Bluetooth Kopfhörer', 'bluetooth-kopfhoerer', 'Kabellose Kopfhörer mit hervorragender Klangqualität und Noise Cancelling Funktion.', 'Premium Bluetooth Kopfhörer', 199.99, 149.99, 20, 1],
            [$modeId, 'Herren T-Shirt Basic', 'herren-t-shirt-basic', 'Komfortables Basic T-Shirt aus 100% Baumwolle. Perfekt für den Alltag.', 'Basic T-Shirt aus Baumwolle', 29.99, null, 50, 1],
            [$modeId, 'Damen Jeans Slim', 'damen-jeans-slim', 'Moderne Slim-Fit Jeans für Damen. Hochwertige Verarbeitung und perfekte Passform.', 'Slim-Fit Jeans für Damen', 79.99, 59.99, 30, 1],
            [$haushaltId, 'Kaffeemaschine Deluxe', 'kaffeemaschine-deluxe', 'Vollautomatische Kaffeemaschine mit integriertem Mahlwerk. Für perfekten Kaffeegenuss.', 'Vollautomatische Kaffeemaschine', 899.99, 699.99, 8, 1],
            [$haushaltId, 'Staubsauger Robot', 'staubsauger-robot', 'Intelligenter Saugroboter mit App-Steuerung. Saugt selbstständig und kehrt zur Ladestation zurück.', 'Smarter Saugroboter', 399.99, null, 15, 1],
            [$sportId, 'Fitness Tracker', 'fitness-tracker', 'Moderner Fitness Tracker mit Herzfrequenzmessung, GPS und Schlafüberwachung.', 'Smartwatch für Sport', 149.99, 99.99, 25, 1],
            [$sportId, 'Yoga Matte Premium', 'yoga-matte-premium', 'Hochwertige Yoga Matte aus umweltfreundlichen Materialien. Rutschfest und langlebig.', 'Premium Yoga Matte', 49.99, null, 40, 1],
            [$buecherId, 'Das große Kochbuch', 'das-grosse-kochbuch', 'Umfassendes Kochbuch mit über 500 Rezepten aus aller Welt. Für Anfänger und Profis.', 'Kochbuch mit 500+ Rezepten', 39.99, 29.99, 20, 1],
        ]);

        // Add some demo product images (placeholder URLs)
        $products = $this->db->createCommand('SELECT id FROM {{%products}}')->queryAll();
        foreach ($products as $product) {
            $this->insert('{{%product_images}}', [
                'product_id' => $product['id'],
                'image_url' => 'https://via.placeholder.com/400x300?text=Produkt+' . $product['id'],
                'alt_text' => 'Produktbild',
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%product_images}}');
        $this->delete('{{%products}}');
        $this->delete('{{%categories}}');
    }
}
