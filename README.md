# eloquent-orderable

The Orderable package provides functionality to manage the order of records in Laravel Eloquent models. Please note that this package currently only supports MySQL databases.

## Installation

You can install the package via composer:

```bash
composer require snowrunescape/orderable
```

## Usage

Use the Orderable trait in your Eloquent model.
Optionally, define the $sortable property in your model class to customize column names and options.

### Example

```php
use Illuminate\Database\Eloquent\Model;
use SnowRunescape\Orderable\Orderable;

class YourModel extends Model
{
    use Orderable;

    /**
     * The Orderable trait configuration.
     *
     * @var array
     */
    protected $sortable = [
        "column_name" => "order",
        "sort_direction" => "ASC",
        "sort_when_creating" => true,
        "apply_global_scope" => true,
        "scope_columns" => [],
    ];
}

$item = YourModel::find(1);

YourModel::updateOrder($item, 2);
```

## License

eloquent-orderable is made available under the MIT License (MIT). Please see [License File](https://github.com/SnowRunescape/eloquent-orderable/blob/master/LICENSE) for more information.
