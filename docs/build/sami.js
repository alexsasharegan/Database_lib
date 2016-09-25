
(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:Database" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Database.html">Database</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Database_Exceptions" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Database/Exceptions.html">Exceptions</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Database_Exceptions_BadQuery" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Database/Exceptions/BadQuery.html">BadQuery</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Database_Utils" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Database/Utils.html">Utils</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Database_Utils_Timer" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Database/Utils/Timer.html">Timer</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Database_Where" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Database/Where.html">Where</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Database_Where_Clause" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Database/Where/Clause.html">Clause</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Database_MySQL" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Database/MySQL.html">MySQL</a>                    </div>                </li>                            <li data-name="class:Database_TableBuilder" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Database/TableBuilder.html">TableBuilder</a>                    </div>                </li>                            <li data-name="class:Database_TableField" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Database/TableField.html">TableField</a>                    </div>                </li>                            <li data-name="class:Database_Where" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Database/Where.html">Where</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "Database.html", "name": "Database", "doc": "Namespace Database"},{"type": "Namespace", "link": "Database/Exceptions.html", "name": "Database\\Exceptions", "doc": "Namespace Database\\Exceptions"},{"type": "Namespace", "link": "Database/Utils.html", "name": "Database\\Utils", "doc": "Namespace Database\\Utils"},{"type": "Namespace", "link": "Database/Where.html", "name": "Database\\Where", "doc": "Namespace Database\\Where"},
            
            {"type": "Class", "fromName": "Database\\Exceptions", "fromLink": "Database/Exceptions.html", "link": "Database/Exceptions/BadQuery.html", "name": "Database\\Exceptions\\BadQuery", "doc": "&quot;Class BadQuery&quot;"},
                                                        {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method_getQuery", "name": "Database\\Exceptions\\BadQuery::getQuery", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method_setQuery", "name": "Database\\Exceptions\\BadQuery::setQuery", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method_getLogs", "name": "Database\\Exceptions\\BadQuery::getLogs", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method_setLogs", "name": "Database\\Exceptions\\BadQuery::setLogs", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method___construct", "name": "Database\\Exceptions\\BadQuery::__construct", "doc": "&quot;BadQuery constructor.&quot;"},
                    {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method___toString", "name": "Database\\Exceptions\\BadQuery::__toString", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Exceptions\\BadQuery", "fromLink": "Database/Exceptions/BadQuery.html", "link": "Database/Exceptions/BadQuery.html#method_jsonSerialize", "name": "Database\\Exceptions\\BadQuery::jsonSerialize", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Database", "fromLink": "Database.html", "link": "Database/MySQL.html", "name": "Database\\MySQL", "doc": "&quot;Class MySQL&quot;"},
                                                        {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_connect", "name": "Database\\MySQL::connect", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_SQLDateToPath", "name": "Database\\MySQL::SQLDateToPath", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getSQLDate", "name": "Database\\MySQL::getSQLDate", "doc": "&quot;Get the current time formatted for MySQL&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_now", "name": "Database\\MySQL::now", "doc": "&quot;Get the current time formatted for MySQL&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_randomString", "name": "Database\\MySQL::randomString", "doc": "&quot;Generate a variable-length random string of alpha characters. Defaults to lower case.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_randomAlphaNumeric", "name": "Database\\MySQL::randomAlphaNumeric", "doc": "&quot;Generate a variable-length random string of alpha-numeric characters. Defaults to lower case.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_randomHex", "name": "Database\\MySQL::randomHex", "doc": "&quot;Generate a variable-length random string of hexidecimal characters. Defaults to lower case.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method___construct", "name": "Database\\MySQL::__construct", "doc": "&quot;MySQL constructor. Pass in the path to a json config file.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_switchDatabase", "name": "Database\\MySQL::switchDatabase", "doc": "&quot;Switch a database by passing the database name.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method___destruct", "name": "Database\\MySQL::__destruct", "doc": "&quot;Automatically closes the connection to mysql&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_query", "name": "Database\\MySQL::query", "doc": "&quot;Perform a raw SQL query. Does not return the result object,\nbut instead returns the MySQL instance for chaining.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_select", "name": "Database\\MySQL::select", "doc": "&quot;Select a record set by passing in a table,\nan array of fields to select,\nand [optional] a where clause.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_insert", "name": "Database\\MySQL::insert", "doc": "&quot;Insert a single row by passing the table,\nan associative array of fields =&gt; values to insert,\nand [optional] a boolean whether or not to update on duplicate with the same data set\nReturns the insert id or false&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_insertOnUpdate", "name": "Database\\MySQL::insertOnUpdate", "doc": "&quot;Insert a single row by passing the table,\nan associative array of fields =&gt; values to insert,\nand an associative array of fields =&gt; values to update on duplicate\nReturns the insert id or false&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_bulkInsert", "name": "Database\\MySQL::bulkInsert", "doc": "&quot;Insert an array of models (associative arrays) into the given table in one query.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_update", "name": "Database\\MySQL::update", "doc": "&quot;Update records by passing the table,\nan associative array of fields =&gt; values to update,\nand [optional] a where clause\nReturns the number of rows affected by the query&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_delete", "name": "Database\\MySQL::delete", "doc": "&quot;Delete records by passing the table\nand [optional] a where clause\nReturns the number of rows affected by the query&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_createTable", "name": "Database\\MySQL::createTable", "doc": "&quot;Create a table&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_dropTable", "name": "Database\\MySQL::dropTable", "doc": "&quot;Drop a table&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getLastQuery", "name": "Database\\MySQL::getLastQuery", "doc": "&quot;Returns the last query executed&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_iterateResult", "name": "Database\\MySQL::iterateResult", "doc": "&quot;Takes an iterator (closure) to process each row of returned data.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_reduceResult", "name": "Database\\MySQL::reduceResult", "doc": "&quot;Applies a function against an accumulator ($carry) and each row of the last returned mysqli result object to\nreduce it to a single value.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_mapResult", "name": "Database\\MySQL::mapResult", "doc": "&quot;Creates a new array with the results of calling the provided function on each row of the last returned mysqli\nresult object.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getResult", "name": "Database\\MySQL::getResult", "doc": "&quot;Returns the last query&#039;s result object&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getError", "name": "Database\\MySQL::getError", "doc": "&quot;Get the last error from mysqli&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_numRows", "name": "Database\\MySQL::numRows", "doc": "&quot;Returns the number of rows from the last query\n(must be done before iterating over the result)&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_affectedRows", "name": "Database\\MySQL::affectedRows", "doc": "&quot;Returns the number of rows affected by the last query\n(inserts, updates, deletes)&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_insertId", "name": "Database\\MySQL::insertId", "doc": "&quot;Returns the last inserted id from mysqli&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_lastId", "name": "Database\\MySQL::lastId", "doc": "&quot;Returns the last inserted id\nAlias of MySQL::insertId&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_id", "name": "Database\\MySQL::id", "doc": "&quot;Returns the last inserted id\nAlias of MySQL::insertId&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getInsertedIds", "name": "Database\\MySQL::getInsertedIds", "doc": "&quot;Inserts performed using the insert methods\nsave the inserted ids to an array.&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getIds", "name": "Database\\MySQL::getIds", "doc": "&quot;ALias for getInsertedIds&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getLogs", "name": "Database\\MySQL::getLogs", "doc": "&quot;Gets an array of all the queries performed by the instance&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getColumns", "name": "Database\\MySQL::getColumns", "doc": "&quot;Get an array of all the columns in a given table [, and a given database ]&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_buildInserts", "name": "Database\\MySQL::buildInserts", "doc": "&quot;Escapes an associative array of [ keys =&gt; values ]\nand returns an array with the escaped data\nat $returnArray[&#039;keys&#039;] &amp;amp; $returnArray[&#039;values&#039;]&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_buildUpdate", "name": "Database\\MySQL::buildUpdate", "doc": "&quot;Escapes an associative array of [ keys =&gt; values ]\nand returns an Update Statement string with the escaped data&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_escape", "name": "Database\\MySQL::escape", "doc": "&quot;Escapes a value&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_escapeColumnName", "name": "Database\\MySQL::escapeColumnName", "doc": "&quot;Wraps a column name in backticks&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_escapeColumnNames", "name": "Database\\MySQL::escapeColumnNames", "doc": "&quot;Wraps an array of columns in backticks\nand returns the escaped array&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_escapeKeyValuePairs", "name": "Database\\MySQL::escapeKeyValuePairs", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_isAssoc", "name": "Database\\MySQL::isAssoc", "doc": "&quot;Checks if an array is associative by asserting against numeric indices&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_isNumericArray", "name": "Database\\MySQL::isNumericArray", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getCreatedTables", "name": "Database\\MySQL::getCreatedTables", "doc": "&quot;Returns an array of any tables created by this instance&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_getLastCreatedTable", "name": "Database\\MySQL::getLastCreatedTable", "doc": "&quot;Returns the [string] name of the last created table&quot;"},
                    {"type": "Method", "fromName": "Database\\MySQL", "fromLink": "Database/MySQL.html", "link": "Database/MySQL.html#method_dropAllCreatedTables", "name": "Database\\MySQL::dropAllCreatedTables", "doc": "&quot;Drop all tables created by this instance.&quot;"},
            
            {"type": "Class", "fromName": "Database", "fromLink": "Database.html", "link": "Database/TableBuilder.html", "name": "Database\\TableBuilder", "doc": "&quot;Class TableBuilder&quot;"},
                                                        {"type": "Method", "fromName": "Database\\TableBuilder", "fromLink": "Database/TableBuilder.html", "link": "Database/TableBuilder.html#method_getTableName", "name": "Database\\TableBuilder::getTableName", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\TableBuilder", "fromLink": "Database/TableBuilder.html", "link": "Database/TableBuilder.html#method___construct", "name": "Database\\TableBuilder::__construct", "doc": "&quot;TableBuilder constructor.&quot;"},
                    {"type": "Method", "fromName": "Database\\TableBuilder", "fromLink": "Database/TableBuilder.html", "link": "Database/TableBuilder.html#method_addField", "name": "Database\\TableBuilder::addField", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\TableBuilder", "fromLink": "Database/TableBuilder.html", "link": "Database/TableBuilder.html#method_render", "name": "Database\\TableBuilder::render", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Database", "fromLink": "Database.html", "link": "Database/TableField.html", "name": "Database\\TableField", "doc": "&quot;Class TableField&quot;"},
                                                        {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_getForeignKeyTable", "name": "Database\\TableField::getForeignKeyTable", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_getForeignKeyField", "name": "Database\\TableField::getForeignKeyField", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method___construct", "name": "Database\\TableField::__construct", "doc": "&quot;TableField constructor: takes the name of the table to be created.&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_isType", "name": "Database\\TableField::isType", "doc": "&quot;Sets the type for this field.&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_autoIncrement", "name": "Database\\TableField::autoIncrement", "doc": "&quot;Sets AUTO_INCREMENT on the field&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_unsigned", "name": "Database\\TableField::unsigned", "doc": "&quot;Designates an unsigned field&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_timestamp", "name": "Database\\TableField::timestamp", "doc": "&quot;Designates a timestamp field&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_defaultsTo", "name": "Database\\TableField::defaultsTo", "doc": "&quot;Sets a default value on the field&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_notNull", "name": "Database\\TableField::notNull", "doc": "&quot;Disallows NULL on a field (default for boolean fields)&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_foreignKey", "name": "Database\\TableField::foreignKey", "doc": "&quot;Imposes a foreign key constraint on the field&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_hasForeignKey", "name": "Database\\TableField::hasForeignKey", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method___toString", "name": "Database\\TableField::__toString", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\TableField", "fromLink": "Database/TableField.html", "link": "Database/TableField.html#method_getUserInputType", "name": "Database\\TableField::getUserInputType", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Database\\Utils", "fromLink": "Database/Utils.html", "link": "Database/Utils/Timer.html", "name": "Database\\Utils\\Timer", "doc": "&quot;Class Timer&quot;"},
                                                        {"type": "Method", "fromName": "Database\\Utils\\Timer", "fromLink": "Database/Utils/Timer.html", "link": "Database/Utils/Timer.html#method___construct", "name": "Database\\Utils\\Timer::__construct", "doc": "&quot;Timer constructor.&quot;"},
                    {"type": "Method", "fromName": "Database\\Utils\\Timer", "fromLink": "Database/Utils/Timer.html", "link": "Database/Utils/Timer.html#method_stop", "name": "Database\\Utils\\Timer::stop", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Utils\\Timer", "fromLink": "Database/Utils/Timer.html", "link": "Database/Utils/Timer.html#method_getTime", "name": "Database\\Utils\\Timer::getTime", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Utils\\Timer", "fromLink": "Database/Utils/Timer.html", "link": "Database/Utils/Timer.html#method_getCurrentTime", "name": "Database\\Utils\\Timer::getCurrentTime", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Utils\\Timer", "fromLink": "Database/Utils/Timer.html", "link": "Database/Utils/Timer.html#method_lap", "name": "Database\\Utils\\Timer::lap", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Utils\\Timer", "fromLink": "Database/Utils/Timer.html", "link": "Database/Utils/Timer.html#method___toString", "name": "Database\\Utils\\Timer::__toString", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Database", "fromLink": "Database.html", "link": "Database/Where.html", "name": "Database\\Where", "doc": "&quot;Class Where&quot;"},
                                                        {"type": "Method", "fromName": "Database\\Where", "fromLink": "Database/Where.html", "link": "Database/Where.html#method___construct", "name": "Database\\Where::__construct", "doc": "&quot;Where constructor.&quot;"},
                    {"type": "Method", "fromName": "Database\\Where", "fromLink": "Database/Where.html", "link": "Database/Where.html#method_addClause", "name": "Database\\Where::addClause", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Where", "fromLink": "Database/Where.html", "link": "Database/Where.html#method_parseClause", "name": "Database\\Where::parseClause", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Where", "fromLink": "Database/Where.html", "link": "Database/Where.html#method___toString", "name": "Database\\Where::__toString", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Database\\Where", "fromLink": "Database/Where.html", "link": "Database/Where/Clause.html", "name": "Database\\Where\\Clause", "doc": "&quot;Class Clause&quot;"},
                                                        {"type": "Method", "fromName": "Database\\Where\\Clause", "fromLink": "Database/Where/Clause.html", "link": "Database/Where/Clause.html#method_isValidType", "name": "Database\\Where\\Clause::isValidType", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Where\\Clause", "fromLink": "Database/Where/Clause.html", "link": "Database/Where/Clause.html#method___construct", "name": "Database\\Where\\Clause::__construct", "doc": "&quot;Clause constructor.&quot;"},
                    {"type": "Method", "fromName": "Database\\Where\\Clause", "fromLink": "Database/Where/Clause.html", "link": "Database/Where/Clause.html#method___toString", "name": "Database\\Where\\Clause::__toString", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Where\\Clause", "fromLink": "Database/Where/Clause.html", "link": "Database/Where/Clause.html#method_add", "name": "Database\\Where\\Clause::add", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Database\\Where\\Clause", "fromLink": "Database/Where/Clause.html", "link": "Database/Where/Clause.html#method_add_array", "name": "Database\\Where\\Clause::add_array", "doc": "&quot;&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


