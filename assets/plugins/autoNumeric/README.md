## What is [autoNumeric](https://autonumeric.github.io)?

autoNumeric is a standalone Javascript library that provides live *as-you-type* formatting for international numbers and currencies.

[![NPM][nodei-image]][nodei-url]
<br>
[![NPM version][npm-image]][npm-url]
[![Downloads][downloads-image]][downloads-url]
[![jsDelivr Hits][jsdelivr-image]][jsdelivr-url]
[![Build Status][travis-image]][travis-url]
[![Known Vulnerabilities][snyk-image]][snyk-url]
[![Coverage Status][coveralls-image]][coveralls-url]
<br>
[![Gitter chat][gitter-image]][gitter-url]

The latest stable branch is always on `master`. Currently this is version [4.*](https://github.com/autoNumeric/autoNumeric/tree/master).<br>
if you want to live dangerously, you can check out the latest development version in the `next` [branch](https://github.com/autoNumeric/autoNumeric/tree/next).<br>
<br>
For older stable versions, please take a look [here](#older-versions).<br>
Alternatively, you can **use our [guide](doc/HowToUpgradeToV4.md) for upgrading from version `1.9`/`2` to version `4`.**<br><br>
Moreover, you can take a look at what could be the next features coming to autoNumeric on our [project](https://github.com/autoNumeric/autoNumeric/projects) page *(feel free to participate!)*.

#### Highlights
autoNumeric main features are :
- **Easy** to use and configure
```js
// Initialization
new AutoNumeric('.myInput', { currencySymbol : '$' });
```
- Very **high configurability** (more than 40 [options](#options) available)
```js
// The options are...optional :)
const autoNumericOptionsEuro = {
    digitGroupSeparator        : '.',
    decimalCharacter           : ',',
    decimalCharacterAlternative: '.',
    currencySymbol             : '\u202f€',
    currencySymbolPlacement    : AutoNumeric.options.currencySymbolPlacement.suffix,
    roundingMethod             : 'U',
};

// Initialization
new AutoNumeric(domElement, autoNumericOptionsEuro);
```
- User experience oriented ; using autoNumeric just **feels right and natural**, specially with the function chaining feature
```js
anElement.french()
         .set(42)
         .update({ options })
         .formSubmitJsonNumericString(callback)
         .clear();
```
- Supports most **international** numeric formats and currencies<br>*(If the one you use is not supported yet, open an [issue](https://github.com/autoNumeric/autoNumeric/issues/new) and we'll add it as soon as possible!)*
- The mobile Android Chrome browser is now partially supported!

*And also:*
- Any number of different formats can be used at the same time on the same page.<br>Each input can be configured by either setting the options as HTML5 data attributes, or directly passed as an argument in the Javascript code
- The settings can easily be changed at *any* time using the `update` method or via a callback
- autoNumeric supports `input` elements as well as most text elements with the `contenteditable` attribute, allowing you to place formatted numbers and currencies on just about any part of your pages
- AutoNumeric elements *can be linked together* allowing you to perform one action on multiple elements at once
- 8 pre-defined [currency options](#predefined-language-options) as well as 31 [common options](#predefined-common-options) allows you to directly use autoNumeric by skipping the option configuration step
- 26 built-in [methods](#methods) gives you the flexibility needed to use autoNumeric to its full potential
- 22 [global methods](#perform-actions-globally-on-a-shared-list-of-autonumeric-elements) that allows to control sets of AutoNumeric-managed elements at once
- 21 additional [methods](#methods) specialized for managing form management and submission
- 17 [static functions](#static-methods) provided by the `AutoNumeric` class
- And more than 40 [options](#options) allowing you to precisely customize your currency format and behavior

With that said, autoNumeric supports most international numeric formats and currencies including those used in Europe, Asia, and North and South America.

****

## Table of contents
- [Getting started](#getting-started)
  - [Installation](#installation)
  - [How to use?](#how-to-use)
    - [In the browser](#in-the-browser)
    - [In another script](#in-another-script)
  - [On which elements can it be used?](#on-which-elements-can-it-be-used)
    - [On `<input>` elements](#on-input-elements)
    - [On `contenteditable`-enabled elements](#on-contenteditable-enabled-elements)
    - [On other DOM elements](#on-other-dom-elements)
- [Initialization](#initialization)
  - [Initialize one AutoNumeric object](#initialize-one-autonumeric-object)
  - [Initialize multiple AutoNumeric objects at once](#initialize-multiple-autonumeric-objects-at-once)
- [Options](#options)
  - [Predefined options](#predefined-options)
    - [Predefined language options](#predefined-language-options)
    - [Predefined common options](#predefined-common-options)
    - [Predefined style rules](#predefined-style-rules)
      - [Positive and negative](#positive-and-negative)
      - [Range from 0 to 100, in 4 steps](#range-from-0-to-100-in-4-steps)
      - [Odd and even](#odd-and-even)
      - [Small range around zero, from -1 to 1](#small-range-around-zero-from--1-to-1)
      - [Custom callbacks](#custom-callbacks)
  - [Special options](#special-options)
    - [noEventListeners](#noeventlisteners)
    - [readOnly](#readonly)
  - [Options update](#options-update)
- [Methods](#methods)
  - [Instantiated methods](#instantiated-methods)
    - [Set, get, format, unformat and other usual AutoNumeric functions](#set-get-format-unformat-and-other-usual-autonumeric-functions)
      - [Using callback functions with `get*` methods](#using-callback-functions-with-get-methods)
    - [Un-initialize the AutoNumeric element](#un-initialize-the-autonumeric-element)
    - [Node manipulation](#node-manipulation)
    - [Format and unformat other numbers or DOM elements with an existing AutoNumeric element](#format-and-unformat-other-numbers-or-dom-elements-with-an-existing-autonumeric-element)
    - [Initialize other DOM Elements](#initialize-other-dom-elements)
    - [Perform actions globally on a shared list of AutoNumeric elements](#perform-actions-globally-on-a-shared-list-of-autonumeric-elements)
      - [Using callback functions with `global.get*` methods](#using-callback-functions-with-globalget-methods)
    - [Form functions](#form-functions)
    - [Function chaining](#function-chaining)
  - [Static methods](#static-methods)
- [Event lifecycle](#event-lifecycle)
- [Questions](#questions)
- [How to contribute?](#how-to-contribute)
- [Dependencies](#dependencies)
- [Older versions](#older-versions)
- [Related projects](#related-projects)
- [Other documentation](#other-documentation)
- [Licence](#licence)
- [Support](#support)

## Getting started

### Installation
You can install autoNumeric with your preferred dependency manager:
```bash
# with `yarn` :
yarn add autonumeric
# or with `npm` :
npm install autonumeric --save
```

### How to use?

#### In the browser
Simply include **autoNumeric** in your html `<header>` tag.<br>No other files or libraries are required ; autoNumeric has **no dependency**.

```html
<script src="autoNumeric.min.js" type="text/javascript"></script>
<!-- ...or, you may also directly use a CDN :-->
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.0.1"></script>
<!-- ...or -->
<script src="https://unpkg.com/autonumeric@4.0.1/dist/autoNumeric.min.js"></script>
```

#### In another script
If you want to use AutoNumeric in your code, you can import the `src/AutoNumeric.js` file as an ES6 module using:
```js
import AutoNumeric from 'AutoNumeric';
```

Then you can initialize autoNumeric with or without options :
```js
// autoNumeric with the defaults options
anElement = new AutoNumeric(domElement);

// autoNumeric with specific options being passed
anElement = new AutoNumeric(domElement, { options });

// autoNumeric with a css selector and a pre-defined language options
anElement = new AutoNumeric('.myCssClass > input').french();
```
*(See the available language list [here](#predefined-language-options))*

You're done!

*Note : an AutoNumeric object can be initialized in various ways, check those out [here](#initialization)*

### On which elements can it be used?

autoNumeric can be used in two ways ;
- with event listeners when used on `<input>` elements or on `contenteditable`-enabled elements making them reactive (in a *read/write* mode), or
- without event listeners when used on DOM elements not having the `contenteditable` attribute set to `true`, essentially acting as a *format-once-and-forget*-*read only* mode.

#### On `<input>` elements
When used on an `<input>` element, you'll be able to interact with its value and get a formatted input value *as-you-type*, using the full power of autoNumeric.

Please note than due to browser constraints, only the following supported `<input>` *types* are supported :
- `text`,
- `tel`,
- `hidden`, or
- no type specified at all

```html
<input type='text' value="1234.56">
<input type='tel' value="1234.56">
<input type='hidden' value="1234.56">
<input value="1234.56">
```

Note : the `number` type is **not** supported simply because autoNumeric formats numbers as strings (ie. `'123.456.789,00 &#8364;'`) that this input type does not allow.


#### On `contenteditable`-enabled elements
Any element in the following `allowedTagList` that support the `contenteditable` attribute can be initialized by autoNumeric.
This means that anywhere on a page, on any DOM element, you can harness the power of autoNumeric which will allow you to mask the user inputs.

Given the following html code...:
```html
<p id="editableDom" contenteditable="true">12345678.9012</p>
```
you can initialize this `<p>` element with autoNumeric:
```js
new AutoNumeric('#editableDom').french();
```
...and it will act exactly like an `<input>` element controlled by autoNumeric.

#### On other DOM elements

You can use autoNumeric to format a DOM element value **once** on load.<br>
This means it will then not react to any user interaction.

The following elements are accepted :
```js
const allowedTagList = [
    'b', 'caption', 'cite', 'code', 'const', 'dd', 'del', 'div', 'dfn', 'dt', 'em', 'h1', 'h2', 'h3',
    'h4', 'h5', 'h6', 'ins', 'kdb', 'label', 'li', 'option', 'output', 'p', 'q', 's', 'sample',
    'span', 'strong', 'td', 'th', 'u'
]
```

## Initialization

An AutoNumeric object can be initialized in various ways.

### Initialize one AutoNumeric object

It always takes either a DOM element reference as its first argument, or a css string selector.<br>
*Note: only one element can be selected this way, since under the hood `document.querySelector` is called, and this only return one element.*<br>
*If you need to be able to select and initialize multiple elements in one call, then consider using the static [`AutoNumeric.multiple()`](#initialize-multiple-autonumeric-objects-at-once) function*

```js
anElement = new AutoNumeric(domElement); // With the default options
anElement = new AutoNumeric(domElement, { options }); // With one option object
anElement = new AutoNumeric(domElement, 'euroPos'); // With a named pre-defined string
anElement = new AutoNumeric(domElement, [{ options1 }, 'euroPos', { options2 }]); // With multiple option objects (the latest option overwriting the previous ones)
anElement = new AutoNumeric(domElement).french(); // With one pre-defined language object
anElement = new AutoNumeric(domElement).french({ options });// With one pre-defined language object and additional options that will override those defaults

// ...or init and set the value in one call :
anElement = new AutoNumeric(domElement, 12345.789); // With the default options, and an initial value
anElement = new AutoNumeric(domElement, 12345.789, { options });
anElement = new AutoNumeric(domElement, '12345.789', { options });
anElement = new AutoNumeric(domElement, 12345.789, 'euroPos');
anElement = new AutoNumeric(domElement, 12345.789, [{ options1 }, 'euroPos', { options2 }]);
anElement = new AutoNumeric(domElement, null, { options }); // With a null initial value
anElement = new AutoNumeric(domElement, 12345.789).french({ options });
anElement = new AutoNumeric(domElement, 12345.789, { options }).french({ options }); // Not really helpful, but possible

// The AutoNumeric constructor class can also accept a string as a css selector. Under the hood this use `QuerySelector` and limit itself to only the first element it finds.
anElement = new AutoNumeric('.myCssClass > input');
anElement = new AutoNumeric('.myCssClass > input', { options });
anElement = new AutoNumeric('.myCssClass > input', 'euroPos');
anElement = new AutoNumeric('.myCssClass > input', [{ options1 }, 'euroPos', { options2 }]);
anElement = new AutoNumeric('.myCssClass > input', 12345.789);
anElement = new AutoNumeric('.myCssClass > input', 12345.789, { options });
anElement = new AutoNumeric('.myCssClass > input', 12345.789, 'euroPos');
anElement = new AutoNumeric('.myCssClass > input', 12345.789, [{ options1 }, 'euroPos', { options2 }]);
anElement = new AutoNumeric('.myCssClass > input', null, { options }); // With a null initial value
anElement = new AutoNumeric('.myCssClass > input', 12345.789).french({ options });
```
*Note: AutoNumeric also accepts a [limited tag list](#on-other-dom-elements) that it will format on page load, but without adding any event listeners if their `contenteditable` attribute is not set to `true`*

### Initialize multiple AutoNumeric objects at once
If you know you want to initialize multiple elements in one call, you must then use the static `AutoNumeric.multiple()` function:
```js
// Init multiple DOM elements in one call (and possibly pass multiple values that will be mapped to each DOM element)
[anElement1, anElement2, anElement3] = AutoNumeric.multiple([domElement1, domElement2, domElement3], { options });
[anElement1, anElement2, anElement3] = AutoNumeric.multiple([domElement1, domElement2, domElement3], 'euroPos');
[anElement1, anElement2, anElement3] = AutoNumeric.multiple([domElement1, domElement2, domElement3], [{ options }, 'euroPos']);
[anElement1, anElement2, anElement3] = AutoNumeric.multiple([domElement1, domElement2, domElement3], 12345.789, { options });
[anElement1, anElement2, anElement3] = AutoNumeric.multiple([domElement1, domElement2, domElement3], 12345.789, [{ options }, 'euroPos']);
[anElement1, anElement2, anElement3] = AutoNumeric.multiple.french([domElement1, domElement2, domElement3], [12345.789, 234.78, null], { options });
[anElement1, anElement2, anElement3] = AutoNumeric.multiple.french([domElement1, domElement2, domElement3], [12345.789, 234.78, null], [{ options }, 'euroPos']);

// Special case, if a <form> element is passed (or any other 'parent' (or 'root') DOM element), then autoNumeric will initialize each child `<input>` elements recursively, ignoring those referenced in the `exclude` attribute
[anElement1, anElement2] = AutoNumeric.multiple({ rootElement: formElement }, { options });
[anElement1, anElement2] = AutoNumeric.multiple({ rootElement: formElement, exclude : [hiddenElement, tokenElement] }, { options });
[anElement1, anElement2] = AutoNumeric.multiple({ rootElement: formElement, exclude : [hiddenElement, tokenElement] }, [12345.789, null], { options });

// If you want to select multiple elements via a css selector, then you must use the `multiple` function. Under the hood `QuerySelectorAll` is used.
[anElement1, anElement2] = AutoNumeric.multiple('.myCssClass > input', { options }); // This always return an Array, even if there is only one element selected
[anElement1, anElement2] = AutoNumeric.multiple('.myCssClass > input', [null, 12345.789], { options }); // Idem above, but with passing the initial values too
```

*Note: Using an array of option objects / pre-defined names will always merge those settings. The resulting setting objet will then be applied to all the selected elements ; they will share the exact same settings.*

## Options
Multiple options allow you to customize precisely how a form input will format your key strokes as you type :

| Option           | Description | Default Value |
| :---------------- | :-----------:  | :-----------:  |
| `allowDecimalPadding` | Allow padding the decimal places with zeros. If set to `'floats'`, padding is only done when there are some decimals. | `true` |
| `caretPositionOnFocus` | Determine where should be positioned the caret on focus | `null` |
| `createLocalList` | Determine if a local list of AutoNumeric objects must be kept when initializing the elements and others | `true` |
| `currencySymbol` | Currency symbol | `''` |
| `currencySymbolPlacement` | Placement of the currency sign, relative to the number (as a prefix or a suffix) | `'p'` |
| `decimalCharacter` | Decimal separator character | `'.'` |
| `decimalCharacterAlternative` | Allow to declare alternative decimal separator which is automatically replaced by the *real* decimal character (useful in countries where the keyboard numeric pad have a period as the decimal character) | `null` |
| `decimalPlaces` | Defines the default number of decimal places to show on the formatted value, and to keep as the precision for the `rawValue`. This can be overridden by the other `decimalPlaces*` options. | `2` |
| `decimalPlacesRawValue` | Defines how many decimal places should be kept for the raw value. This is the precision for float values. | `null` |
| `decimalPlacesShownOnBlur` | The number of decimal places to show when *unfocused* | `null` |
| `decimalPlacesShownOnFocus` | The number of decimal places to show when *focused* | `null` |
| `defaultValueOverride` | Helper option for the ASP.NET-specific postback issue | `null` |
| `digitalGroupSpacing` | Digital grouping for the thousand separator | `'3'` |
| `digitGroupSeparator` | Thousand separator character  | `','` |
| `divisorWhenUnfocused` | Define the number that will divide the current value shown when unfocused | `null` |
| `emptyInputBehavior` | Define what to display when the input value is empty (possible options are `null`, `focus`, `press`, `always` and `zero`) | `'focus'` |
| `failOnUnknownOption ` | This option is the 'strict mode' (aka 'debug' mode), which allows autoNumeric to strictly analyse the options passed, and fails if an unknown options is used in the settings object. | `false` |
| `formatOnPageLoad` | Determine if the default value will be formatted on initialization | `true` |
| `historySize` | Determine how many undo states an AutoNumeric object should keep in memory | `20` |
| `isCancellable` | Determine if the user can *'cancel'* the last modifications done to the element value when using the `Escape` key | `true` |
| `leadingZero` | Controls the leading zero behavior (possible options are `allow`, `deny` and `keep`) | `'deny'` |
| `maximumValue` | Maximum possible value | `'9999999999999.99'` |
| `minimumValue` | Minimum possible value | `'-9999999999999.99'` |
| `modifyValueOnWheel` | Determine if the element value can be incremented / decremented with the mouse wheel. The wheel behavior is modified with the `wheelStep` option. | `true` |
| `negativeBracketsTypeOnBlur` | Adds brackets `[]`, parenthesis `()`, curly braces `{}`, chevrons `<>`, angle brackets `〈,〉`, Japanese quotation marks `｢,｣`, half brackets `⸤,⸥`, white square brackets `⟦,⟧`, quotation marks `‹,›` or guillemets `«,»` on negative values when unfocused | `null` |
| `negativePositiveSignPlacement` | Placement of negative/positive sign relative to the currency symbol (possible options are `l` (left), `r` (right), `p` (prefix) and `s` (suffix)) | `null` |
| `noEventListeners` | Defines if the element should have event listeners activated on it | `false` |
| `onInvalidPaste` | Manage how autoNumeric react when the user tries to paste an invalid number (possible options are `error`, `ignore`, `clamp`, `truncate` or `replace`) | `'error'` |
| `outputFormat` | Defines the localized output format of the `get`, `getString` & `getArray` methods | `null` |
| `overrideMinMaxLimits` | Override minimum and maximum limits (possible options are `ceiling`, `floor` and `ignore`) | `null` |
| `rawValueDivisor` | Define the number that will divide the formatted value into the raw value (ie. when displaying `'1.23%'`, the raw value kept is `0.0123` if `rawValueDivisor` is set to `100`) | `null` |
| `readOnly` | Defines if the `<input>` element should be set as read only on initialization | `false` |
| `roundingMethod` | Method used for rounding. The possible options are `S` (Round-Half-Up Symmetric (default)), `A` (Round-Half-Up Asymmetric), `s` (Round-Half-Down Symmetric (lower case s)), `a` (Round-Half-Down Asymmetric (lower case a)), `B` (Round-Half-Even 'Bankers Rounding'), `U` (Round Up 'Round-Away-From-Zero'), `D` (Round Down 'Round-Toward-Zero' - same as truncate), `C` (Round to Ceiling 'Toward Positive Infinity'), `F` (Round to Floor 'Toward Negative Infinity'), `N05` (Rounds to the nearest .05 (same as `'CHF'` used in v1.9.* and still valid)), `U05` (Rounds up to next .05), `D05` (Rounds down to next .05) | `'S'` |
| `saveValueToSessionStorage` | Allow the `decimalPlacesShownOnFocus` value to be saved into session storage | `false` |
| `selectNumberOnly` | Determine if the select all keyboard command will select the complete input text, or only the input numeric value | `false` |
| `selectOnFocus` | Defines if the element value should be selected on focus | `true` |
| `serializeSpaces` | Defines how the serialize functions should treat spaces when serializing (convert them to `'%20'` or `'+'`) | `'+'` |
| `showOnlyNumbersOnFocus` | Remove the thousand separator, currency symbol and suffix on focus | `false` |
| `showPositiveSign` | Allow the positive sign symbol `+` to be displayed for positive numbers | `false` |
| `showWarnings` | Defines if warnings should be shown | `true` |
| `styleRules` | Defines the rules that calculate the CSS class(es) to apply on the element, based on the raw unformatted value.<br>This can also be used to call callbacks whenever the `rawValue` is updated. | `null` |
| `suffixText` | Additional text suffix that is added after the number | `''` |
| `symbolWhenUnfocused` | Symbol placed as a suffix when unfocused. This is used in combination with the `divisorWhenUnfocused` option. | `null` |
| `unformatOnHover` | Defines if the element value should be unformatted when the user hover his mouse over it while holding the `Alt` key | `true` |
| `unformatOnSubmit` | Removes formatting on submit event | `false` |
| `wheelStep` | Used in conjonction with the `modifyValueOnWheel` option, this allow to either define a *fixed* step (ie. `1000`), or a *progressive* one | `'progressive'` |

#### Predefined options

Sometimes you do not want to have to configure every single aspect of your format, specially if it's a common one.<br>Hence, we provide multiple default options for the most common currencies and number formats.

##### Predefined language options

autoNumeric provides predefined language options to format currencies.<br>
You can set the pre-defined language option like so:
```js
// Use the methods
new AutoNumeric('.mySelector > input').french();
// ...or just create the AutoNumeric object with the language option
new AutoNumeric('.mySelector > input', AutoNumeric.getPredefinedOptions().French);
```

Currently, the predefined language options are:

| | Option name |
| :---------------- | :---------------- |
| :fr: | `French` |
| :es: | `Spanish` |
| :us: | `NorthAmerican` |
| :uk: | `British` |
| <span>&#x1f1e8;&#x1f1ed;</span> | `Swiss` |
| :jp: | `Japanese` |
| :cn: | `Chinese` |
| <span>&#x1f1e7;&#x1f1f7;</span> | `Brazilian` |

If you feel a common currency option is missing, please [create a pull request](https://github.com/autoNumeric/autoNumeric/compare) and we'll add it!

##### Predefined common options

Moreover, autoNumeric provides the following common options:

| Option name | Description | Examples |
| :---------------- | :---------------- | ----------------: |
| `dotDecimalCharCommaSeparator` | Set the decimal character as a dot `.` and the group separator as a comma `,` | `1,234.56` |
| `commaDecimalCharDotSeparator` | Set the decimal character as a comma `,` and the group separator as a dot `.` | `1.234,56` |
| `integer` | Set the minimum and maximum value so that only an integer can be entered, without any decimal places available | `42`, `-42` |
| `integerPos` | Set the minimum and maximum value so that only a positive integer can be entered | `42` |
| `integerNeg` | Set the minimum and maximum value so that only a negative integer can be entered | `-42` |
| `float` | Set the minimum and maximum value so that a float can be entered, without the default `2` decimal places | `1.234`, `-1.234` |
| `floatPos` | Set the minimum and maximum value so that only a positive float can be entered | `1.234` |
| `floatNeg` | Set the minimum and maximum value so that only a negative float can be entered | `-1.234` |
| `numeric` | Format the value as a numeric string (with no digit group separator, and a dot for the decimal point) | `1234.56` |
| `numericPos` | Idem above, but only allow positive values | `1234.56` |
| `numericNeg` | Idem above, but only allow negative values | `-1234.56` |
| `euro` | Same configuration than `French` | `1.234,56 €` |
| `euroPos` | Idem above, but only allow positive values | `1.234,56 €` |
| `euroNeg` | Idem above, but only allow negative values | `-1.234,56 €` |
| `euroSpace` | Same configuration than `French` except a space is used for the group separator instead of the dot | `1 234,56 €` |
| `euroSpacePos` | Idem above, but only allow positive values | `1 234,56 €` |
| `euroSpaceNeg` | Idem above, but only allow negative values | `-1 234,56 €` |
| `dollar` | Same configuration than `NorthAmerican`  | `$1,234.56` |
| `dollarPos` | Idem above, but only allow positive values | `$1,234.56` |
| `dollarNeg` | Idem above, but only allow negative values | `-$1,234.56` |
| `percentageEU2dec` | Same configuration than `French`, but display a percent `%` sign instead of the currency sign, with `2` decimal places | `12,34 %` |
| `percentageEU2decPos` | Idem above, but only allow positive values | `12,34 %` |
| `percentageEU2decNeg` | Idem above, but only allow negative values | `-12,34 %` |
| `percentageEU3dec` | Same configuration than `French`, but display a percent `%` sign instead of the currency sign, with `3` decimal places | `12,345 %` |
| `percentageEU3decPos` | Idem above, but only allow positive values | `12,345 %` |
| `percentageEU3decNeg` | Idem above, but only allow negative values | `-12,345 %` |
| `percentageUS2dec` | Same configuration than `NorthAmerican`, but display a percent `%` sign instead of the currency sign, with `2` decimal places | `12.34%` |
| `percentageUS2decPos` | Idem above, but only allow positive values | `12.34%` |
| `percentageUS2decNeg` | Idem above, but only allow negative values | `-12.34%` |
| `percentageUS3dec` | Same configuration than `NorthAmerican`, but display a percent `%` sign instead of the currency sign, with `3` decimal places | `12.345%` |
| `percentageUS3decPos` | Idem above, but only allow positive values | `12.345%` |
| `percentageUS3decNeg` | Idem above, but only allow negative values | `-12.345%` |

You can set those pre-defined options like so:
```js
new AutoNumeric('.mySelector > input', AutoNumeric.getPredefinedOptions().integerPos);
```

##### Predefined style rules

With the `styleRules` option, you can define the rules that add or remove the CSS class(es) from the element, based on the raw unformatted value.<br>This option can also be used to define custom callbacks in the `userDefined` attribute, that will be called whenever the `rawValue` is updated.

Predefined styles are available so you do not have to create them:

###### Positive and negative
Sets the `'autoNumeric-positive'` css class whenever the raw value is positive.<br>
Sets the `'autoNumeric-negative'` css class whenever the raw value is negative.
```js
new AutoNumeric(domElement, { styleRules: AutoNumeric.options.styleRules.positiveNegative });
```

###### Range from 0 to 100, in 4 steps
Sets the `'autoNumeric-red'` css class whenever the raw value is between `0` and `25` excluded.<br>
Sets the `'autoNumeric-orange'` css class whenever the raw value is between `25` and `50` excluded.<br>
Sets the `'autoNumeric-yellow'` css class whenever the raw value is between `50` and `75` excluded.<br>
Sets the `'autoNumeric-green'` css class whenever the raw value is between `75` and `100` excluded.
```js
new AutoNumeric(domElement, { styleRules: AutoNumeric.options.styleRules.range0To100With4Steps });
```

###### Odd and even
Sets the `'autoNumeric-even'` css class whenever the raw value is even.<br>
Sets the `'autoNumeric-odd'` css class whenever the raw value is odd.
```js
new AutoNumeric(domElement, { styleRules: AutoNumeric.options.styleRules.evenOdd });
```

###### Small range around zero, from -1 to 1
Sets the `'autoNumeric-small-negative'` css class whenever the raw value is between `-1` and `0` excluded.<br>
Sets the `'autoNumeric-zero'` css class whenever the raw value is equal to `0`.<br>
Sets the `'autoNumeric-small-positive'` css class whenever the raw value is between `0` excluded and `1`.
```js
new AutoNumeric(domElement, { styleRules: AutoNumeric.options.styleRules.rangeSmallAndZero });
```

###### Custom callbacks
Custom callbacks can be defined and will be called every time the *raw value* is updated.<br>
You can add as many callbacks you want in the `userDefined` attribute of the `styleRules` object in the options.<br>
Each `userDefined` array entry should at least provide a function as the `callback` attribute.<br>
This `callback` function is passed the `rawValue` as the single parameter (except if `classes` is `null` or `undefined`, see below).

Depending of what type of data the `callback` function returns, and what the content of the `classes` attribute is, it will either uses CSS class names defined in the `classes` attribute, or just call the `callback` with the current AutoNumeric object passed as a parameter if `classes` is `null` or `undefined`.

| # | Callback return type | `classes` content | Result |
| :----------------: | :----------------: | :-----------: | :-----------:  |
| 1 | a `boolean` | a single `String` | If `true`, add the single class defined in `classes`. If `false` removes it. |
| 2 | a `boolean` | an `Array` with 2 values (array indexes) | If `true`, add the first element of the array, otherwise the second |
| 3 | an `integer` | an `Array` with multiple values (array indexes) | Will add the selected CSS class `classes[index]`, and remove the others |
| 4 | an `Array` of `integer` | an `Array` with multiple values (array indexes) | Will add *all* the given selected CSS classes, and remove the others |
| 5 | ∅ | `null` or `undefined` | There, the callback have access to the current AutoNumeric object passed as its argument, which means you are free to do *whatever you want* from here! |

See the following examples:
```js
const options = {
    styleRules : {
        userDefined: [
            // 1) If 'classes' is a string, set it if `true`, remove it if `false`
            { callback: rawValue => { return true; }, classes: 'thisIsTrue' },
            // 2) If 'classes' is an array with only 2 elements, set the first class if `true`, the second if `false`
            { callback: rawValue => rawValue % 2 === 0, classes: ['autoNumeric-even', 'autoNumeric-odd'] },
            // 3) Return only one index to use on the `classes` array (here, 'class3')
            { callback: rawValue => { return 2; }, classes: ['class1', 'class2', 'class3'] },
            // 4) Return an array of indexes to use on the `classes` array (here, 'class1' and 'class3')
            { callback: rawValue => { return [0, 2]; }, classes: ['class1', 'class2', 'class3'] },
            // 5) If 'classes' is `undefined` or `null`, then the callback is called with the AutoNumeric object passed as a parameter
            { callback: anElement => { return anElement.getFormatted(); } },
        ],
    },
}
```

#### Special options

###### noEventListeners
Using the `noEventListeners` option allow autoNumeric to only format without adding any event listeners to an input, or any other DOM elements (that the function would accept as a parameter). This would be useful for read-only values for instance.
```js
// Initialize without setting up any event listeners
anElement = new AutoNumeric(domElement, 12345.789, { options }).remove(); // This is the default existing way of doing that...
// ...but you can also directly pass a special option `noEventListeners` to prevent the initial creation of those event listeners
anElement = new AutoNumeric(domElement, 12345.789, { noEventListeners: true });
```
In the latter case, it initialize the AutoNumeric element, except it does not add any event listeners. Which means it format the value only once and then let the user modify it freely.<br>*Note: The value can then be formatted via a call to `set`.*

###### readOnly
AutoNumeric can initialize an `<input>` element with the `readonly` property by setting the `readOnly` option to `true` in the settings:
```js
anElement = new AutoNumeric(domElement, 12345.789, { readOnly: true });
```

For more detail on how to use each options, please take a look at the detailed comments in the source code for the `defaultSettings` object.

#### Options update
Options can be added and/or modified after the initialization has been done.

Either by passing an option object that contains multiple options,
```js
anElement.update({ moreOptions });
anElement.update(AutoNumeric.getPredefinedOptions().NorthAmerican); // Update the settings (and immediately reformat the element accordingly)
```

by passing multiple option objects, the latter overwriting the settings from the former ones...
```js
anElement.update({ moreOptions1 }, { moreOptions2 }, { moreOptions3 });
```

...or by changing the options one by one (or by calling a pre-defined option object).
```js
anElement.options.minimumValue('12343567.89');
anElement.options.allowDecimalPadding(false);
```

At any point, you can reset the options by calling the `options.reset()` method.
This effectively drop any previous options you could have set, then load back the default settings.
```js
anElement.options.reset();
```

Lastly, the option object can be accessed directly, thus allowing to query each options globally too
```js
anElement.getSettings(); // Return the options object containing all the current autoNumeric settings in effect
```

## Methods
autoNumeric provides numerous methods to access and modify the element value, formatted or unformatted, at any point in time.
<br>It does so by providing access to those [methods](#instantiated-methods) via the AutoNumeric object class (declared as an ES6 Module).

First. you need to get a reference to the AutoNumeric module that you need to import:
```js
import AutoNumeric from 'autoNumeric.min';
```

Then you'll be able to access either the methods on the instantiated AutoNumeric object, or the [static functions](#static-methods) directly by using the `AutoNumeric` class.

### Instantiated methods

#### Set, get, format, unformat and other usual AutoNumeric functions

The following functions are available on all autoNumeric-managed elements:

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `set` | Set the value (that will be formatted immediately) | `anElement.set(42.76);` |
| `set` | Set the value and update the setting in one go | `anElement.set(42.76, { options });` |
| `set` | Set the value, but do not save the new state in the history table (used for undo/redo actions) | `anElement.set(42.76, { options }, false);` |
| `setUnformatted` | Set the value (that will not be formatted immediately) | `anElement.setUnformatted(42.76);` |
| `setUnformatted` | Set the value and update the setting in one go (the value will not be formatted immediately) | `anElement.setUnformatted(42.76, { options });` |
| `getNumericString` | Return the unformatted number as a string | `anElement.getNumericString();` |
| `get` | Alias for the `.getNumericString()` method | `anElement.get();` |
| `getFormatted` | Return the formatted string | `anElement.getFormatted();` |
| `getNumber` | Return the unformatted number as a number (**Warning**: If you are manipulating a number bigger than [`Number.MAX_SAFE_INTEGER`](https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/Number/MAX_SAFE_INTEGER), you **will** encounter problems if you try to retrieve it as a number and not a string) | `anElement.getNumber();` |
| `getLocalized` | Return the localized unformatted number as a string | `anElement.getLocalized();` |
| `getLocalized` | Return the localized unformatted number as a string, using the outputFormat option override passed as a parameter | `anElement.getLocalized(forcedOutputFormat);` |
| `getLocalized` | Idem above, but with a callback function and a forced outputFormat | `anElement.getLocalized(forcedOutputFormat, callback);` |
| `getLocalized` | Idem above, but with a callback function | `anElement.getLocalized(callback);` |
| `get*` | Pass the result of the `get*` function to the given callback, see [here](#using-callback-functions-with-get-methods) | `anElement.get*(funcCallback);` |
| `reformat` | Force the element to reformat its value again (in case the formatting has been lost) | `anElement.reformat();` |
| `unformat` | Remove the formatting and keep only the raw unformatted value in the element (as a numeric string) | `anElement.unformat();` |
| `unformatLocalized` | Remove the formatting and keep only the localized unformatted value in the element | `anElement.unformatLocalized();` |
| `unformatLocalized` | Idem above, but using the outputFormat option override passed as a parameter | `anElement.unformatLocalized(forcedOutputFormat);` |
| `isPristine` | Return `true` if the current value is the same as when the element got initialized | `anElement.isPristine();` |
| `select` | Select the formatted element content, based on the `selectNumberOnly` option | `anElement.select();` |
| `selectNumber` | Select only the numbers in the formatted element content, leaving out the currency symbol, whatever the value of the `selectNumberOnly` option | `anElement.selectNumber();` |
| `selectInteger` | Select only the integer part in the formatted element content, whatever the value of `selectNumberOnly` | `anElement.selectInteger(); ` |
| `selectDecimal` | Select only the decimal part in the formatted element content, whatever the value of `selectNumberOnly` | `anElement.selectDecimal();` |
| `clear` | Reset the element value to the empty string '' (or the currency sign, depending on the `emptyInputBehavior` option value) | `anElement.clear();` |
| `clear` | Reset the element value to the empty string '' as above, no matter the `emptyInputBehavior` option value | `anElement.clear(true);` |

*Note: Most of them can be [chained](#function-chaining) together, if needed.*

##### Using callback functions with `get*` methods

All `get*` methods can accept a callback function as its argument (those methods being `get`, `getNumericString`, `getFormatted`, `getNumber` and `getLocalized`).
That callback is passed two parameters, the result of the `get*` method as its first argument, and the AutoNumeric object as its second.

This allows you to directly use the result of the `get*` functions without having to declare a temporary variable like so:
```js
function sendToServer(value) {
    ajax(value);
}

console.log(`The value ${anElement.getNumber(sendToServer)} has been sent to the server.`);
```

In other words,
```js
// Using:
anElement.getNumericString(funcCallback);

// Is equivalent to doing:
const result = anElement.getNumericString();
funcCallback(result, anElement);
```

*Note: The callback function behavior is slightly different when called on [multiple elements](#perform-actions-globally-on-a-shared-list-of-autonumeric-elements) via `global.get*` methods.*

#### Un-initialize the AutoNumeric element

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `remove` | Remove the autoNumeric listeners from the element (previous name : 'destroy'). Keep the element content intact. | `anElement.remove();` |
| `wipe` | Remove the autoNumeric listeners from the element, and reset its value to '' | `anElement.wipe();` |
| `nuke` | Remove the autoNumeric listeners from the element, and delete the DOM element altogether | `anElement.nuke();` |


#### Node manipulation

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `node` | Return the DOM element reference of the autoNumeric-managed element | `anElement.node();` |
| `parent` | Return the DOM element reference of the parent node of the autoNumeric-managed element | `anElement.parent();` |
| `detach` | Detach the current AutoNumeric element from the shared 'init' list (which means any changes made on that local shared list will not be transmitted to that element anymore) | `anElement.detach();` |
| `detach` | Idem above, but detach the given AutoNumeric element, not the current one | `anElement.detach(otherAnElement);` |
| `attach` | Attach the given AutoNumeric element to the shared local 'init' list. When doing that, by default the DOM content is left untouched. The user can force a reformat with the new shared list options by passing a second argument to `true`. | `anElement.attach(otherAnElement, reFormat = true);` |


#### Format and unformat other numbers or DOM elements with an existing AutoNumeric element

You can use any AutoNumeric element to format or unformat other numbers or DOM elements.

This allows to format or unformat numbers, strings or directly other DOM elements without having to specify the options each time, since the current AutoNumeric object already has those settings set.

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `formatOther` | This use the same function signature that when using the static AutoNumeric method directly (cf. below: `AutoNumeric.format`), but without having to pass the options | `anElement.formatOther(12345, { options });` |
| `formatOther` | Idem above, but apply the formatting to the DOM element content directly | `anElement.formatOther(domElement5, { options }); ` |
| `unformatOther` | This use the same function signature that when using the static AutoNumeric method directly (cf. below: `AutoNumeric.unformat`), but without having to pass the options | `anElement.unformatOther('1.234,56 €', { options });` |
| `unformatOther` | Idem above, but apply the unformatting to the DOM element content directly | `anElement.unformatOther(domElement5, { options });` |


#### Initialize other DOM Elements

Once you have an AutoNumeric element already setup correctly with the right options, you can use it as many times you want to initialize as many other DOM elements as needed (this works only on elements that can be managed by autoNumeric).

Whenever `init` is used to initialize other DOM elements, a shared 'local' list of those elements is stored in the AutoNumeric objects.<br>This allows for neat things like modifying all those *linked* AutoNumeric elements globally, with one call.

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `init` | Use an existing AutoNumeric element to initialize another single DOM element with the same options | `const anElement2 = anElement.init(domElement2);` |
| `init` | If `true` is set as the second argument, then the newly generated AutoNumeric element will not share the same local element list as `anElement` | `const anElement2 = anElement.init(domElement2, true);` |
| `init` | Use an existing AutoNumeric element to initialize multiple other DOM elements from an Array, with the same options | `const anElementsArray = anElement.init([domElement2, domElement3, domElement4]);` |
| `init` | Use an existing AutoNumeric element to initialize multiple other DOM elements from a CSS selector, with the same options | `const anElementsArray = anElement.init('.currency');` |


#### Perform actions globally on a shared list of AutoNumeric elements

This local list can be used to perform global operations on all those AutoNumeric elements, with one function call.<br>
To do so, you must call the wanted function by prefixing `.global` before the method name (ie. `anElement.global.set(42)`).<br>
Below are listed all the supported methods than can be called globally:

```js
anElement.global.set(2000); // Set the value 2000 in all the autoNumeric-managed elements that are shared on this element
anElement.global.setUnformatted(69);
[result1, result2, result3] = anElement.global.get(); // Return an array of results
[result1, result2, result3] = anElement.global.getNumericString(); // Return an array of results
[result1, result2, result3] = anElement.global.getFormatted(); // Return an array of results
[result1, result2, result3] = anElement.global.getNumber(); // Return an array of results
[result1, result2, result3] = anElement.global.getLocalized(); // Return an array of results
anElement.global.reformat();
anElement.global.unformat();
anElement.global.unformatLocalized();
anElement.global.unformatLocalized(forcedOutputFormat);
anElement.global.update({ options }); // Update the settings of each autoNumeric-managed elements
anElement.global.update({ options1 }, { options2 }, { options3 }); // Idem above, but accepts as many option objects as needed
anElement.global.isPristine(); // Return `true` is *all* the autoNumeric-managed elements are pristine, if their raw value hasn't changed
anElement.global.isPristine(false); // Idem as above, but also checks that the formatted value hasn't changed
anElement.global.clear(); // Clear the value in all the autoNumeric-managed elements that are shared on this element
anElement.global.remove();
anElement.global.wipe();
anElement.global.nuke();
```

The shared local list also provide list-specific methods to manipulate it:
```js
anElement.global.has(domElementOrAutoNumericObject); // Return `true` if the given AutoNumeric object (or DOM element) is in the local AutoNumeric element list
anElement.global.addObject(domElementOrAutoNumericObject); // Add an existing AutoNumeric object (or DOM element) to the local AutoNumeric element list, using the DOM element as the key
anElement.global.removeObject(domElementOrAutoNumericObject); // Remove the given AutoNumeric object (or DOM element) from the local AutoNumeric element list, using the DOM element as the key
anElement.global.removeObject(domElementOrAutoNumericObject, true); // Idem above, but keep the current AutoNumeric object in the local list if it's removed by itself
anElement.global.empty(); // Remove all elements from the shared list, effectively emptying it
anElement.global.empty(true); // Idem above, but instead of completely emptying the local list of each AutoNumeric objects, each one of those keeps itself in its own local list
[anElement0, anElement1, anElement2, anElement3] = anElement.global.elements(); // Return an array containing all the AutoNumeric elements that have been initialized by each other
anElement.global.getList(); // Return the `Map` object directly
anElement.global.size(); // Return the number of elements in the local AutoNumeric element list
```

##### Using callback functions with `global.get*` methods

Like for their `get*` methods [counterparts](#using-callback-functions-with-get-methods), `global.get*` methods accepts a callback function.
However, the callback is executed only **once** and is passed an array of the `get*` function results as its first argument, while the AutoNumeric object being passed as its second one.

```js
// Using:
anElement.global.getNumericString(funcCallback);

// Is equivalent to doing:
const [result1, result2, result3] = anElement.global.getNumericString();
funcCallback([result1, result2, result3], anElement);
```

#### Form functions

autoNumeric elements provide special functions to manipulate the form they are a part of.
Those special functions really work on the parent `<form>` element, instead of the `<input>` element itself. 

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `form` | Return a reference to the parent `<form>` element, `null` if it does not exist | `anElement.form();` |
| `form(forcedSearch)` | Idem above, but will force a new search for the parent `<form>` element, discarding any previously found one | `anElement.form(true);` |
| `formNumericString` | Return a string in standard URL-encoded notation with the form input values being unformatted | `anElement.formNumericString();` |
| `formFormatted` | Return a string in standard URL-encoded notation with the form input values being formatted | `anElement.formFormatted();` |
| `formLocalized` | Return a string in standard URL-encoded notation with the form input values, with localized values | `anElement.formLocalized();` |
| `formLocalized(forcedOutputFormat)` | Idem above, but with the possibility of overriding the `outputFormat` option | `anElement.formLocalized(forcedOutputFormat);` |
| `formArrayNumericString` | Return an array containing an object for each form `<input>` element, with the values as numeric strings | `anElement.formArrayNumericString();` |
| `formArrayFormatted` | Return an array containing an object for each form `<input>` element, with the values as formatted strings | `anElement.formArrayFormatted();` |
| `formArrayLocalized` | Return an array containing an object for each form `<input>` element, with the values as localized numeric strings | `anElement.formArrayLocalized();` |
| `formArrayLocalized(forcedOutputFormat)` | Idem above, but with the possibility of overriding the `outputFormat` option | `anElement.formArrayLocalized(forcedOutputFormat);` |
| `formJsonNumericString` | Return a JSON string containing an object representing the form input values. This is based on the result of the `formArrayNumericString()` function. | `anElement.formJsonNumericString();` |
| `formJsonFormatted` | Return a JSON string containing an object representing the form input values. This is based on the result of the `formArrayFormatted()` function. | `anElement.formJsonFormatted();` |
| `formJsonLocalized` | Return a JSON string containing an object representing the form input values. This is based on the result of the `formArrayLocalized()` function. | `anElement.formJsonLocalized();` |
| `formJsonLocalized(forcedOutputFormat)` | Idem above, but with the possibility of overriding the `outputFormat` option | `anElement.formJsonLocalized(forcedOutputFormat);` |
| `formUnformat` | Unformat all the autoNumeric-managed elements that are a child to the parent <form> element of this `anElement` input, to numeric strings | `anElement.formUnformat();` |
| `formUnformatLocalized` | Unformat all the autoNumeric-managed elements that are a child to the parent <form> element of this `anElement` input, to localized strings | `anElement.formUnformatLocalized();` |
| `formReformat` | Reformat all the autoNumeric-managed elements that are a child to the parent <form> element of this `anElement` input | `anElement.formReformat();` |

The following functions can either take a callback, or not. If they don't, the default `form.submit()` function will be called.

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `formSubmitNumericString(callback)` | Run the `callback(value)` with `value` being equal to the result of `formNumericString()` | `anElement.formSubmitNumericString(callback);` |
| `formSubmitFormatted(callback)` | Run the `callback(value)` with `value` being equal to the result of `formFormatted()` | `anElement.formSubmitFormatted(callback);` |
| `formSubmitLocalized(callback)` | Run the `callback(value)` with `value` being equal to the result of `formLocalized()` | `anElement.formSubmitLocalized(callback);` |
| `formSubmitLocalized(forcedOutputFormat, callback)` | Idem above, but with the possibility of overriding the `outputFormat` option | `anElement.formSubmitLocalized(forcedOutputFormat, callback);` |

For the following methods, the callback is mandatory:

| Method           | Description | Call example |
| :----------------: | :-----------:  | :-----------:  |
| `formSubmitArrayNumericString(callback)` | Run the `callback(value)` with `value` being equal to the result of `formArrayNumericString()` | `anElement.formSubmitArrayNumericString(callback);` |
| `formSubmitArrayFormatted(callback)` | Run the `callback(value)` with `value` being equal to the result of `formArrayFormatted()` | `anElement.formSubmitArrayFormatted(callback);` |
| `formSubmitArrayLocalized(callback, forcedOutputFormat)` | Idem above, but with the possibility of overriding the `outputFormat` option | `anElement.formSubmitArrayLocalized(callback, forcedOutputFormat);` |
| `formSubmitJsonNumericString(callback)` | Run the `callback(value)` with `value` being equal to the result of `formJsonNumericString()` | `anElement.formSubmitJsonNumericString(callback);` |
| `formSubmitJsonFormatted(callback)` | Run the `callback(value)` with `value` being equal to the result of `formJsonFormatted()` | `anElement.formSubmitJsonFormatted(callback);` |
| `formSubmitJsonLocalized(callback, forcedOutputFormat)` | Idem above, but with the possibility of overriding the `outputFormat` option | `anElement.formSubmitJsonLocalized(callback, forcedOutputFormat);` |


#### Function chaining

Most of those instantiated functions can be chained which allow to be less verbose and more concise.

```js
// On one element
anElement.french()
         .set(42)
         .update({ options })
         .formSubmitJsonNumericString(callback)
         .clear();

// On multiple elements
anElement.global.set(72)
         .global.clear()
         .set(25)
         .global.getNumericString();
```

### Static methods

Without having to initialize any AutoNumeric object, you can directly use the static `AutoNumeric` class functions.

| Method           | Description | Call example |
| :---------------- | :-----------:  | :-----------:  |
| `areSettingsValid` | Return `true` in the settings are valid | `AutoNumeric.areSettingsValid({ options })` |
| `format` | Format the given number with the given options. This returns the formatted value as a string. | `AutoNumeric.format(12345.21, { options });` |
| `format` | Idem above, but using a numeric string as the first parameter | `AutoNumeric.format('12345.21', { options });` |
| `format` | Idem above, but you can pass as many option objects you want to this function, the latter overwriting the previous ones. This allows to correctly format currencies that have a predefined option as its base, but has been slightly modified.  | `AutoNumeric.format('12345.21', { options1 }, { options2 });` |
| `format` | Idem above, using multiple option objects in one array. This way allows for using a pre-defined option name.  | `AutoNumeric.format('12345.21', [{ options1 }, 'euroPos', { options2 }]);` |
| `format` | Format the `domElement` *`value`* (or *`textContent`*) with the given options and returns the formatted value as a string. This does *not* update that element value. | `AutoNumeric.format(domElement, { options });` |
| `formatAndSet` | Format the `domElement` value with the given options and returns the formatted value as a string. This function does update that element value with the newly formatted value in the process. | `AutoNumeric.formatAndSet(domElement, { options });` |
| `getAutoNumericElement` | Return the AutoNumeric object that manages the given DOM element | `AutoNumeric.getAutoNumericElement(domElement)` |
| `getDefaultConfig` | Return the default autoNumeric settings | `AutoNumeric.getDefaultConfig()` |
| `getPredefinedOptions` | Return all the predefined options in one object | `AutoNumeric.getPredefinedOptions()` |
| `getPredefinedOptions` | Return a specific pre-defined language option object | `AutoNumeric.getPredefinedOptions().French` |
| `isManagedByAutoNumeric` | Return `true` if the given DOM element has an AutoNumeric object that manages it. | `AutoNumeric.isManagedByAutoNumeric(domElement);` |
| `localize` | Unformat and localize the given formatted string with the given options. This returns a string. | `AutoNumeric.localize('1.234,56 €', { options });` |
| `localize` | Idem as above, but return the localized DOM element value. This does *not* update that element value. | `AutoNumeric.localize(domElement, { options });` |
| `localizeAndSet` | Unformat and localize the `domElement` value with the given options and returns the localized value as a string. This function does update that element value with the newly localized value in the process. | `AutoNumeric.localizeAndSet(domElement, { options });` |
| `mergeOptions` | Accepts an array of option objects and / or pre-defined option names, and return a single option object where the latter element overwrite the settings from the previous ones | `AutoNumeric.mergeOptions(['euro', { currencySymbol: '#' }]);` |
| `reformatAndSet` | Recursively format all the autoNumeric-managed elements that are a child to the `referenceToTheDomElement` element given as a parameter (this is usually the parent `<form>` element), with the settings of each AutoNumeric elements. | `AutoNumeric.reformatAndSet(referenceToTheDomElement);` |
| `test` | Test if the given domElement is already managed by AutoNumeric (if it is initialized) | `AutoNumeric.test(domElement);` |
| `unformat` | Unformat the given formatted string with the given options. This returns a numeric string. | `AutoNumeric.unformat('1.234,56 €', { options });` |
| `unformat` | Idem above, but you can pass as many option objects you want to this function, the latter overwriting the previous ones. This allows to correctly unformat currencies that have a predefined option as its base, but has been slightly modified. | `AutoNumeric.unformat('241800,02 €', AutoNumeric.getPredefinedOptions().French, { digitGroupSeparator: AutoNumeric.options.digitGroupSeparator.noSeparator });` |
| `unformat` | Idem above, using multiple option objects in one array. This way allows for using a pre-defined option name. | `AutoNumeric.unformat('1.234,56 €', [{ options1 }, 'euroPos', { options2 }]);` |
| `unformat` | Unformat the `domElement` value with the given options and returns the unformatted numeric string. This does *not* update that element value. | `AutoNumeric.unformat(domElement, { options });` |
| `unformatAndSet` | Unformat the `domElement` value with the given options and returns the unformatted value as a numeric string. This function does update that element value with the newly unformatted value in the process. | `AutoNumeric.unformatAndSet(domElement, { options });` |
| `unformatAndSet` | Recursively unformat all the autoNumeric-managed elements that are a child to the `referenceToTheDomElement` element given as a parameter (this is usually the parent `<form>` element) | `AutoNumeric.unformatAndSet(referenceToTheDomElement);` |
| `validate` | Check if the given option object is valid, and that each option is valid as well. This throws an error if it's not. | `AutoNumeric.validate({ options })` |
| `version` | Return the AutoNumeric version number (for debugging purpose) | `AutoNumeric.version();` |


## Event lifecycle

AutoNumeric elements are transparent to the native `input` and `change` events, which means those are correctly sent when using an `<input>` element managed by AutoNumeric.<br>
In addition to the native events, custom events sent by AutoNumeric elements allows you to hook into the formatting lifecycle, as you see fit.

Following are listed how AutoNumeric react to different types of key inputs.

By default a 'normal' printable character input (ie. `'2'` or `','`) will result in those events, in that specific order:
1. `'keydown'`
1. `'autoNumeric:minExceeded'` or `'autoNumeric:maxExceeded'` if there was a range problem
1. `'keypress'` (this is deprecated and will be removed *soon*)
1. `'input'`
1. `'keyup'`
1. `'autoNumeric:formatted'` when all the formatting is done
  - Note: The `event` variable in the event handler provide access to the old and new value in `{ detail : { oldValue: 1, newValue: 2 } }`

When inputting a modifier key (ie. `Control`), we get:
1. `'keydown'`
1. `'keyup'`
1. `'autoNumeric:formatted'` if a change as been detected and that all the formatting is done

If `Delete` or `backspace` is entered, the following events are sent:
1. `'keydown'`
1. `'input'`
1. `'keyup'`
1. `'autoNumeric:formatted'` if a change as been detected and that all the formatting is done

If `Enter` is entered and the value has not changed, the following events are sent:
1. `'keydown'`
1. `'keypress'`
1. `'keyup'`
1. `'autoNumeric:formatted'` if a change as been detected and that all the formatting is done

If `Enter` is entered and the value has been changed, the following events are sent:
1. `'keydown'`
1. `'keypress'`
1. `'change'`
1. `'keyup'`
1. `'autoNumeric:formatted'` if a change as been detected and that all the formatting is done

When a `paste` is done with the mouse, the following events are sent:
1. `'input'`
1. `'keydown'`
1. `'input'`
1. `'keyup'`
1. `'keyup'`
1. `'autoNumeric:formatted'` if a change as been detected and that all the formatting is done

And when a `paste` is done with the keyboard shortcut (ie `ctrl+v`), the following events are sent:
1. `'keydown'`
1. `'keydown'`
1. `'input'`
1. `'keyup'`
1. `'keyup'`
1. `'autoNumeric:formatted'` if a change as been detected and that all the formatting is done

Finally, the `'change'` event is sent on `blur` if the value has been changed since the `focus` one.

*Note: the `AutoNumeric.format()` static function does trigger an `'autoNumeric:formatted'` event if the value that the user is trying to format is outside the `minimumValue` and `maximumValue` range, with the `detail` attribute containing the range error message.*

## Questions
For questions and support please use the [Gitter chat room](https://gitter.im/autoNumeric/autoNumeric) or IRC on Freenode #autoNumeric.<br>The issue list of this repository is **exclusively** for bug reports and feature requests.

****

## How to contribute?
Contributors and pull requests are welcome.<br>Feel free to [contact](#questions) us for any questions.<br>
For more information about how to contribute, please check the [CONTRIBUTING](doc/CONTRIBUTING.md) file which has more details about it.

In a nutshell :
- Get the latest source `git clone -b next https://github.com/autoNumeric/autoNumeric.git && cd autoNumeric && yarn install`
- Make you changes
- Lint, build, and run tests `yarn lint && yarn build && yarn test`
  - If you encounter any linting problems, you can try to automatically fix those with `yarn lintfix` 
- Create a pull request, and we'll check it out as soon as possible!

Again, be sure to check the [CONTRIBUTING](doc/CONTRIBUTING.md) guidelines for more details.<br>
Also, feel free to follow our RSS feeds on [master](https://github.com/autoNumeric/autoNumeric/commits/master.atom) and [next](https://github.com/autoNumeric/autoNumeric/commits/next.atom) to keep up with the latest commits.

## Dependencies
None!

## Older versions
The previous stable autoNumeric version `v2.0.13` can be found [here](https://github.com/autoNumeric/autoNumeric/releases/tag/v2.0.13), while the older `v1.9.46` can be found [here](https://github.com/autoNumeric/autoNumeric/releases/tag/1.9.46).

Check out the [upgrade guide](doc/HowToUpgradeToV4.md) if you need help upgrading from version `1.9`/`2` to version `4`.

## Related projects
For integration into [Rails](http://rubyonrails.org/) projects, you can use the [autonumeric-rails](https://github.com/randoum/autonumeric-rails) project.

For integration with [PHP](http://php.net/) Yii2, take a look at the [extead/yii2-autonumeric](https://github.com/extead/yii2-autonumeric) or [haifahrul/yii2-autonumeric](https://github.com/haifahrul/yii2-autonumeric) projects.

For integration into Javascript frameworks, you can use:
- Vue.js with the [vue-autonumeric component](https://github.com/autoNumeric/vue-autoNumeric),
- Angular with the [angular-currency](https://github.com/BuffCoder/angular-currency) directive,
- Meteor with [meteor-autonumeric](https://github.com/gibson/meteor-autonumeric), and
- Ember with [ember-autonumeric](https://github.com/ykaragol/ember-autonumeric).

*Note: Some of those projects may lag with the latest AutoNumeric stable version, or even be incomplete*

## Other documentation
The old and outdated documentation can be found in the [Documentation](doc/Documentation.md) file.<br>
For some examples and an option code generator for the old v1.9.* version, take a look [here](http://www.decorplanit.com/plugin/).

## Licence
autoNumeric is an [MIT](http://opensource.org/licenses/MIT)-licensed open source project, and its authors are credited in [AUTHORS](https://github.com/autoNumeric/autoNumeric/blob/master/AUTHORS).

## Support
We'd like to thank [JetBrains](https://www.jetbrains.com/) supporting us by providing open-source licences of their tools, and [Browserstack](https://www.browserstack.com) for allowing us to tests many browsers and platforms.<br>
[![Browserstack][browserstack-image]][browserstack-url]

****

Feel free to donate via Paypal [![Donate][paypal-image]][paypal-url] *(Robert)* or Patreon [![Donate][patreon-image]][patreon-url] *(Alexandre)* to support autoNumeric development.


[downloads-image]: http://img.shields.io/npm/dm/autonumeric.svg
[downloads-url]: http://badge.fury.io/js/autonumeric
[gitter-image]: https://img.shields.io/badge/gitter-autoNumeric%2FautoNumeric-brightgreen.svg
[gitter-url]: https://gitter.im/autoNumeric/autoNumeric
[npm-image]: https://img.shields.io/npm/v/autonumeric.svg
[npm-url]: https://npmjs.org/package/autonumeric
[nodei-image]: https://nodei.co/npm/autonumeric.png?downloads=true&downloadRank=true&stars=true
[nodei-url]: https://nodei.co/npm/autonumeric
[travis-url]: https://travis-ci.org/autoNumeric/autoNumeric
[travis-image]: https://img.shields.io/travis/autoNumeric/autoNumeric.svg
[snyk-image]: https://snyk.io/test/github/autoNumeric/autoNumeric/badge.svg
[snyk-url]: https://snyk.io/test/github/autoNumeric/autoNumeric
[coveralls-image]: https://coveralls.io/repos/github/autoNumeric/autoNumeric/badge.svg?branch=next
[coveralls-url]: https://coveralls.io/github/autoNumeric/autoNumeric?branch=next
[paypal-image]: http://img.shields.io/badge/paypal-donate-brightgreen.svg
[paypal-url]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NCW5699RY8NN2
[patreon-url]: https://www.patreon.com/user?u=4810062
[patreon-image]: https://img.shields.io/badge/patreon-donate-orange.svg
[browserstack-url]: https://www.browserstack.com
[browserstack-image]: http://i.imgur.com/kjddhbT.png
[jsdelivr-image]: https://data.jsdelivr.com/v1/package/npm/autonumeric/badge?style=rounded
[jsdelivr-url]: https://www.jsdelivr.com/package/npm/autonumeric
