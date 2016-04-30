// HomeIslandManager.cpp : Defines the entry point for the console application.
//

#include <boost/lambda/lambda.hpp>
#include <iostream>
#include <iterator>
#include <algorithm>

int main()
{
    using namespace boost::lambda;
    typedef std::istream_iterator<int> in;
	
    printf("buss dat");

    std::for_each(in(std::cin), in(), std::cout << (_1 * 3) << " " );
}
