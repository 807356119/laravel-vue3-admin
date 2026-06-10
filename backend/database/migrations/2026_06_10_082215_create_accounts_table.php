<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所属用户');
            $table->string('title')->comment('账户标题');
            $table->string('type')->default('other')->comment('账户类型：bank-银行卡，email-邮箱，social-社交，website-网站，other-其他');
            $table->string('account_name')->nullable()->comment('账户名/持卡人');
            $table->string('account_number')->nullable()->comment('账号/卡号');
            $table->text('password')->nullable()->comment('密码（加密存储）');
            $table->string('bank_name')->nullable()->comment('银行名称');
            $table->string('website')->nullable()->comment('网站地址');
            $table->text('description')->nullable()->comment('备注说明');
            $table->json('images')->nullable()->comment('图片附件');
            $table->json('extra_fields')->nullable()->comment('扩展字段');
            $table->string('category')->default('uncategorized')->comment('分类');
            $table->boolean('is_favorite')->default(false)->comment('是否收藏');
            $table->timestamp('expires_at')->nullable()->comment('过期时间');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('type');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
