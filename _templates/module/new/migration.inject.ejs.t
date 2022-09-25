---
to: database/migrations/<%= new Date().getFullYear() %>_<%= ("0" + (new Date().getMonth() + 1)).slice(-2) %>_<%= ("0" + new Date().getDate()).slice(-2) %>_<%= ("0" + new Date().getHours()).slice(-2) %><%= ("0" + new Date().getMinutes()).slice(-2) %><%= ("0" + new Date().getSeconds()).slice(-2) %>_create_<%= h.changeCase.snake(h.inflection.pluralize(name)) %>_table.php
---
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
<% fields.forEach(function(field){ -%>
            $table-><%= field.type === "string" ? "string" : "" -%><%= field.type === "int" ? "integer" : "" -%><%= field.type === "float" ? "decimal" : "" -%><%= field.type === "timestamp" ? "timestamp" : "" -%><%= field.type === "bool" ? "boolean" : "" -%>('<%= h.changeCase.snake(field.name) -%>');
<% }); -%>
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('<%= h.changeCase.snake(h.inflection.pluralize(name)) %>');
    }
};
