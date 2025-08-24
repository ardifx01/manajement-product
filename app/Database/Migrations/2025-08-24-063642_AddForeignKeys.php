<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeys extends Migration
{
    public function up()
    {
        // Foreign key untuk products -> categories
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'SET NULL');
        
        // Foreign key untuk incoming_items -> products
        $this->forge->addForeignKey('product_id', 'incoming_items', 'products', 'id', 'CASCADE', 'CASCADE');
        
        // Foreign key untuk outgoing_items -> products
        $this->forge->addForeignKey('product_id', 'outgoing_items', 'products', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Hapus foreign keys
        $this->forge->dropForeignKey('products', 'products_category_id_foreign');
        $this->forge->dropForeignKey('incoming_items', 'incoming_items_product_id_foreign');
        $this->forge->dropForeignKey('outgoing_items', 'outgoing_items_product_id_foreign');
    }
}