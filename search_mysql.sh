#!/bin/sh
#!/bin/sh
export JDBC_IMPORTER_HOME=/Users/libingtao/Applications/elasticsearch-jdbc-2.1.1.2
bin=$JDBC_IMPORTER_HOME/bin
lib=$JDBC_IMPORTER_HOME/lib
echo $bin
echo '
{
    "type" : "jdbc",
    "jdbc" : {
        "url" : "jdbc:mysql://localhost:3306/zuiwan_m",
        "user" : "root",
        "password" : "",
        "sql" : "select article_title, article_content from article" ,
        "index" : "zuiwan",
        "type" : "article"
    }
}'  | java \
              -cp "${lib}/*" \
              -Dlog4j.configurationFile=${bin}/log4j2.xml \
              org.xbib.tools.Runner \
              org.xbib.tools.JDBCImporter
echo "sleeping while importer should run..."
sleep 5
curl -XGET 'localhost:9200/zuiwan/article/_search?pretty&q=*'
