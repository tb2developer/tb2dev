#include <iostream>
#include <thread>
#include <queue>
#include <chrono>
#include <mutex>
#include <condition_variable>
#include <sstream>

using namespace std;

queue<string> q_data;

mutex m;
condition_variable cv;

void worker(int tid){

	stringstream sss;
	sss << "thread #" << tid << " started\n";
	cout << sss.str();
	while (true){

		unique_lock<mutex> lock(m);
		cv.wait(lock);

		if (q_data.empty()){
			cout << "thread " << tid << " stopped\n";
			break;
		}

		string data = q_data.front();
		q_data.pop();

		lock.unlock();

		stringstream ss;
		ss << "thread #" << tid << "got message " << data << endl;

		cout << ss.str();

		this_thread::sleep_for(chrono::milliseconds(30));
	}
}

int main(int argc, char const *argv[])
{

	vector<thread*> threads;

	for (int i=0; i < 100; i++){
		thread *t = new thread(worker, i);
		threads.push_back(t);
	}

	for (int i=0; i < 1000; i++){
		q_data.push("asd" + to_string(i));
	}


	while (!q_data.empty()){
		cv.notify_one();
		cout << q_data.size() << endl;
		this_thread::sleep_for(chrono::milliseconds(20));
	}


	for (auto *t: threads){
		t->join();
	}

	cout << "all done\n";

	return 0;
}