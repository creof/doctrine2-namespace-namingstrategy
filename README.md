# doctrine2-namespace-namingstrategy

Highly configurable Doctrine2 naming strategy incorporating class namespace into names. This class generates names in a predictable fashion,
eliminating the need to take database schema into account when naming objects. This is especially useful when dealing with large hierarchical
entity graphs with class name duplication.

## Installion

### composer
Add the "creof/doctrine2-namespace-namingstrategy" package into your composer.json file.

### Other

## Usage

### Doctrine
To use the naming scheme a new instance will need to be created and passed to the entity manager configuration object.

### Symfony
Create a service in your app config and pass the service to the Doctrine entity manager:

    # Doctrine Configuration
    doctrine:
        orm:
            naming_strategy: namespace_naming_strategy

    services:
        namespace_naming_strategy:
            class: CrEOF\Doctrine\ORM\Mapping\NamespaceNamingStrategy
            arguments:
                config:
                    entityNamespaces:
                        - "Acme\Bundle\AcmeBundle"
                    trimAbstract: true

To use the class constants you'll need to define the service in XML or lookup the integer values in the code.

## Configuration

The NamespaceNamingStrategy constructor accepts an array containing one or more of the following options:

 - trimAbstract - Remove "Abstract" from all class names beginning with it. Default false.

 - entityNamespaces - Array containing the root(s) of entity classes. These roots will be removed from generated names.

 - namespaceSeparator - String used to separate namespace path components when generating names. Default is "_" (underscore).

 - joinColumnSeparator - String used to separate the reference column name from the property name. Default is "" (empty).

 - joinTableSeparator - String used to separate entity names when generating join table names. Default is "_" (underscore).

 - trimFallback - One of the two FALLBACK class constants. This setting defines the behavior when no matching namespace in entityNamespaces can be found for a class.

 - case - One of the three CASE class constants. This setting defines whether names are generated in mixed, lower, or upper case.

 - referenceColumnName - This defines the name used/concatenated when generating reference column names. Default is "id".

 - joinColumnOrder - One of the two ORDER class constants. This defines whether the reference column name is prepended or appended to the entity name.

 - joinTableOrder - same as joinColumnOrder but for join tables.

 - splitCamelCase - Use in conjunction with camelCaseSeparator. When true camel cased names will be split using the separator. Default false.

 - camelCaseSeparator - String used to split camel cased words when splitCamelCase is true. Default is "" (empty).

## Caveats

This is a work-in-progress and probably not production ready. Use at your own risk.
