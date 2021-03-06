<?php

namespace EasyShop\Http\Controllers;

use Illuminate\Http\Request;
use EasyShop\Traits\CrudActions;
use EasyShop\Model\Person;

class CustomerController extends Controller
{
    use CrudActions;

    public function __construct()
    {
        $this->initCrud([
            'model' => 'Person',
            'routePrefix' => 'customers',
            'titleCreate' => 'Customer',
        ]);
    }

    public function index(Request $request)
    {
        $records = Person::customer()->index();
        
        return $this->createListView([
            'request' => $request,
            'records' => $records,
        ]);
    }

    protected function beforeStore($request, $data)
    {
        $data['type'] = Person::TYPE_CUSTOMER;
        return $data;
    }
}
