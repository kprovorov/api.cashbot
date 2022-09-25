---
inject: true
to: phpunit.xml
after: \<testsuites\>
skip_if: \<testsuite name=\"<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\"\>
---
        <testsuite name="<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module">
            <directory suffix="Test.php">./app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Tests</directory>
        </testsuite>
