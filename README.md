# Frontend

# Deploying

Configure .env files in both frontend and backend folders
run `npm run build` for frontend
populate the database with the file `file.sql` in the backend folder
and run `php -S localhost:8000 -t backend/public`for backend

The working build is availible at `https://scandiweb-ruddy.vercel.app/`

Fixes:
- [x] Dont commit vendor directory
- [x] Dont commit `.env` files
- [x] Remove unused mutations and queries
- [x] Use PDO insted of Mysqli
- [x] Cant add out of stock items
- [x] Replace color attribute with colored button
- [x] Cant change attributes in cart view
- [x] Reloading while on product page returns an error? (Doesn't for me and vercel)
- [x] After reloading while cart isnt empty the cart overlay is open but should be closed. only open when adding product
- [ ] Order placed successfully shown even if an error gets returned
- [ ] Use models
- [ ] Fetch categories dynamically from the API  
- [ ] Remove any separate GraphQL queries for individual categories  
- [ ] Closer to Figma design

Original TODO:
- [x] The cart overlay button shall be included in the header and visible on all pages
- [x] The button element must have attribute data-testid='cart-btn'
- [x] Item count bubble Shall be visible on the cart overlay button only if there are products in the cart
- [x] If only one item is in the cart, it should be shown as 1 Item . If 2 or more plural forms should be used: X Items
- [x] Placeorder has to perform respective GraphQL mutation that as a result will create a new order in DB
- [x] Once an order is placed, the cart should be emptied
- [x] If a cart is empty the button shall be greyed out and disabled
- [x] When the cart overlay is open the whole page except the header shall be greyed out. Refer to designs for a visual example
- [x] The cart doesn’t need to be saved and doesn’t need to be persistent, it should only be persistent through a single-user session at a minimum. This means saving it in the frontend states and local storage is enough
- [x] Category link must have attribute data-testid='category-link'
- [x] Active category link must have attribute data-testid='active-category-link'
- [x] Container of the cart item attribute must have attribute data-testid='cart-item-attribute-${attribute name in kebab case}'
- [x] Cart item attribute option must have attribute data-testid='cart-item-attribute-${attribute name in kebab case}-${attribute name in kebab case}'
- [x] Selected cart item attribute option must have attribute data-testid='cart-item-attribute-${attribute name in kebab case}-${attribute name in kebab case}-selected'
- [x] Button to decrease quantity must have attribute data-testid='cart-item-amount-decrease'
- [x] Button to increase quantity must have attribute data-testid='cart-item-amount-increase'
- [x] Cart item amount indicator must have attribute data-testid='cart-item-amount'
- [x] Cart total element must have attribute data-testid='cart-total'
- [x] Product card must have attribute data-testid='product-${product name in kebab case}'
- [x] Attribute container must have attribute data-testid='product-attribute-${attribute in kebab case}'
- [x] Gallery must have attribute data-testid='product-gallery'
- [x] Product description must have attribute data-testid='product-description'
- [x] Add to cart button must have attribute data-testid='add-to-cart'

# PSR-1

* Use only `<?php` and `?>` for opening and closing tags.
* Use only `UTF-8` encoding.
* Files SHOULD either declare symbols (classes, functions, constants, etc.) or cause side-effects (e.g. generate output, change .ini settings, etc.) but SHOULD NOT do both.
  * A file SHOULD declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it SHOULD execute logic with side effects, but SHOULD NOT do both.
* Namespaces and classes MUST follow an "autoloading" PSR: [PSR-0, PSR-4].
* Class names MUST be declared in `StudlyCaps`.
* Class constants MUST be declared in all upper case with underscore separators `CLASS_CONSTANT`.
* Method names MUST be declared in `camelCase`.

# PSR-4: Autoloader

* This PSR describes a specification for autoloading classes from file paths.
* It provides a standard way to map namespaces to directories.
* A fully qualified class name has the form `\<NamespaceName>\...<ClassName>`.
* The leading namespace separator and the base namespace correspond to a base directory.
* The sub-namespaces within the namespace correspond to sub-directories within the base directory.
* The class name corresponds to a file name ending in `.php`.
* Underscores in class names have no special meaning in this PSR.

# PSR-12: Extended Coding Style

Example:

```php
<?php

declare(strict_types=1);

namespace Vendor\Package;

use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use Vendor\Package\SomeNamespace\ClassD as D;

use function Vendor\Package\{functionA, functionB, functionC};

use const Vendor\Package\{ConstantA, ConstantB, ConstantC};

class Foo extends Bar implements FooInterface
{
    public function sampleFunction(int $a, int $b = null): array
    {
        if ($a === $b) {
            bar();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }
    }

    final public static function bar()
    {
        // method body
    }
}
```

* This PSR extends and replaces PSR-2, providing a more comprehensive set of coding style rules.
* **Basic:**
  * Code MUST follow all rules in PSR-1.
  * Code MUST use 4 spaces for indentation, and MUST NOT use tabs.
  * There MUST NOT be a hard limit on line length.
  * There SHOULD be a soft limit on line length of 120 characters.
  * There MUST NOT be trailing whitespace at the end of non-blank lines.
  * There MUST be one blank line after the namespace declaration, and there MUST be one blank line after the block of use declarations.
  * Keywords and types that are not `callable` MUST be in lowercase.
* **Classes, Properties, and Methods:**
  * The `extends` and `implements` keywords MUST be declared on the same line as the class name.
  * Opening braces for classes MUST go on the next line, and closing braces MUST go on the next line after the body.
  * Visibility MUST be declared on all properties and methods.
  * The `abstract` and `final` declarations MUST precede the visibility declaration.
  * The `static` declaration MUST come after the visibility declaration.
  * Parentheses for method calls MUST NOT have a space before the opening parenthesis.
  * Opening braces for methods MUST go on the next line, and closing braces MUST go on the next line after the body.
* **Control Structures:**
  * The keyword for a control structure MUST have one space after it.
  * The opening parenthesis for the conditional part of a control structure MUST have one space after it.
  * The closing parenthesis for the conditional part of a control structure MUST have one space before it.
  * The opening brace for the body of the control structure MUST go on the same line as the closing parenthesis, separated by one space.
  * The closing brace for the body of the control structure MUST go on the next line after the body.
* **Type Hinting:**
  * A single space MUST be present after the type declaration when type hinting function or method arguments.
  * Return type declarations MUST have one space after the colon.
* **Anonymous Functions:**
  * The `function` keyword MUST have one space after it.
  * The closing parenthesis for the arguments list MUST have one space after it.
  * The `use` keyword MUST have one space after it.
  * The opening parenthesis for the `use` variables MUST NOT have a space after it.
  * The closing parenthesis for the `use` variables MUST NOT have a space before it.
  * The opening brace for the body MUST go on the same line as the closing parenthesis, separated by one space.
  * The closing brace for the body MUST go on the next line after the body.
