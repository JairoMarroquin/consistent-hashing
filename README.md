# Consistent Hashing for PHP

A lightweight consistent hashing implementation for PHP 8.2+.

> [!NOTE]
> **Status:** This library is currently under development and may not be stable for production use. Feedback and contributions are welcome.

## Installation

Package installation will be available in a future release:

```bash
composer require jmarr/consistent-hashing
```

For development, clone the repository and install the dependencies:

```bash
git clone https://github.com/JairoMarroquin/consistent-hashing.git
cd consistent-hashing
composer install
```

## Usage

```php
use Jmarr\ConsistentHashing\HashRing;

$hashRing = new HashRing();
$hashRing->addNode('node1');
$hashRing->addNode('node2');

$key = 'my-key';
$node = $hashRing->getNode($key);
echo "The node responsible for key '{$key}' is: {$node}";
```

CRC32 is used as the default hashing algorithm.

## Custom Hashers

Hashing algorithms implement the HasherInterface. You can create your own hasher by implementing this interface and passing it to the HashRing constructor.

```php
use Jmarr\ConsistentHashing\HashRing;
use Jmarr\ConsistentHashing\HasherInterface;

final class CustomHasher implements HasherInterface
{
    public function hash(string $key): int
    {
        // Implement your custom hashing logic here
        return 0; // Example: using CRC32 as a placeholder
    }
}
```

The custom hasher can then be used as follows:

```php
$hashRing = new HashRing(new CustomHasher());
```

## Testing

Run the test suite with PHPUnit:

```bash
./vendor/bin/phpunit tests

composer test
```

## Requirements

- PHP 8.2 or higher
- Composer

## License

MIT