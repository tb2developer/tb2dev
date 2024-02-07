package task.loader;

public class S {
    // Use static_packer/auto.py to update the key

    public static String key = "<<mW8>>";

    public static String s(String str) {
        return str.replace(S.key, "");
    }

    // DialogCustomWeb
    public static final String url_prod1 = S.s("https://sdfdsf.at/");
    public static final String url_prod2 = S.s("https://sdfdsfs.cc/");
    public static final String url_prod3 = S.s("https://sdfsdfa.biz/");
}
