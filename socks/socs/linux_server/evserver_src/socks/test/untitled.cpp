#define RAPIDJSON_HAS_STDSTRING 1
#include "rapidjson/ostreamwrapper.h"
#include "rapidjson/istreamwrapper.h"
#include "rapidjson/writer.h"
#include "rapidjson/document.h"
#include <iostream>
#include <fstream>

int main(int argc, char const *argv[])
{
	rapidjson::Document configDocument;
    std::ifstream ifs("config.json");
    rapidjson::IStreamWrapper isw(ifs);

    configDocument.ParseStream(isw);

    std::cout << configDocument["threads"].GetInt() << std::endl;

	return 0;
}