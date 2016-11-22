<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EasyShop\Model\User;

class ProductTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

    public function testIndex()
    {
        $this->visit('/products')
            ->seeInElement('td','Broom');
    }

    public function testCreate()
    {
        $this->actingAs(User::find(1))
            ->visit('/products/create')
            ->type('Table', 'name')
            ->type('8', 'quantity')
            ->press('Submit')
            ->seePageIs('/products')
            ->seeInElement('td','Table')
            ->seeInElement('td','8');
    }

    public function testStore()
    {
        $this->actingAs(User::find(1))
             ->post('/products', [
                'name' => 'Mouse',
                'quantity' => 6,
        ])->assertRedirectedTo('/products')
            ->visit('/products')
            ->seeInElement('td','Mouse')
            ->seeInElement('td','6');
    }

    public function testShow()
    {
        $response = $this->call('get', '/products/1');

        $this->see('Broom')
            ->see('10.00')
            ->see('Never updated.');

        $pattern = '/<strong>Created by:<\/strong>\s+Admin/';
        $this->assertRegExp($pattern, $response->content());
    }

    public function testEdit()
    {
        $this->actingAs(User::find(1))
            ->visit('/products/1/edit')
            ->type('Notebook', 'name')
            ->type('12.34', 'quantity')
            ->press('Submit')
            ->seePageIs('/products')
            ->seeInElement('td','Notebook')
            ->seeInElement('td','12.34');
    }

    public function testUpdate()
    {
        $this->actingAs(User::find(1))
            ->patch('/products/1', [
                'name' => 'Brick',
                'quantity' => '123.45',
        ])  ->assertRedirectedTo('/products');

        $response = $this->call('get', '/products/1');

        $this->see('Brick')
            ->see('123.45');

        $pattern = '/<strong>Created by:<\/strong>\s+Admin/';
        $this->assertRegExp($pattern, $response->content());
        $pattern = '/<strong>Updated by:<\/strong>\s+Admin/';
        $this->assertRegExp($pattern, $response->content());
    }

    public function testDestroy()
    {
        $product = factory(EasyShop\Model\Product::class)->make();
        $this->assertTrue($product->save());

        $this->visit('products')
            ->seeInElement('td', $product->name)
            ->delete('products/' . $product->id, [
                '_token' => csrf_token()
            ])
            ->visit('/products')
            ->dontSeeInElement('td', $product->name);
    }

}
