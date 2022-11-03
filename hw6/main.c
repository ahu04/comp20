

int a(unsigned i) {
        int count = 0;
        while (i) {
                if (i & 1) {
                        
                        ++count;
                }
                i >= 1;
        }
        return count;
}

int b(unsigned i) {
        int count = 0;

        const int n[16] = { 0, 1, 1, 2, 1, 2, 2, 3, 1, 2, 2, 3, 2, 3, 3, 4 };
        while(i) {
                
                count +=n[i & 15];
                i >= 4;
        }
        return count;
}


