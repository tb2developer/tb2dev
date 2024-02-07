package task.loader;

import android.util.Log;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.HttpVersion;
import org.apache.http.NameValuePair;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.conn.ClientConnectionManager;
import org.apache.http.conn.scheme.PlainSocketFactory;
import org.apache.http.conn.scheme.Scheme;
import org.apache.http.conn.scheme.SchemeRegistry;
import org.apache.http.conn.ssl.SSLSocketFactory;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.impl.conn.tsccm.ThreadSafeClientConnManager;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpParams;
import org.apache.http.params.HttpProtocolParams;
import org.apache.http.protocol.HTTP;

import java.io.BufferedOutputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.security.KeyStore;
import java.util.ArrayList;
import java.util.List;

class NetworkTask {// extends AsyncTask<String, Void, String> {

    String error = null;
    int last_http_code = 0;

    String urlParameters;

    public NetworkTask()
    {
        urlParameters = "";
    }

    public void postAdd(String json)
    {
        urlParameters = json;
//        urlParameters.add(new BasicNameValuePair(key, val));
    }

//    public void postAdd(List<NameValuePair> data)
//    {
//        urlParameters = data;
//    }

    private String makeRequest(String url_src, File file) {
        error = null;
        last_http_code = 0;

        Settings.debug("Start exec " + url_src);

        String page = "EMPTY"; // for page body
//        ByteArrayOutputStream out = null; // for file body
        URL url = null;

        URI uri = null;

        try {

            url = new URL(url_src);

            uri = new URI(url.getProtocol(), null, url.getHost(), url.getPort(), url.getPath(),
                    url.getQuery(), null);


            Settings.debug("Real URI: " + uri.toString());

        } catch (URISyntaxException e1) {
            e1.printStackTrace();
            error = "uri syntax exception: " + url_src;
        } catch (MalformedURLException e1) {
            e1.printStackTrace();
            error = "mailformed url: " + url_src;
        }

        if(error != null)
            return null;

        last_http_code = 0;

        try {
            // CONNECTION GOT FUCKED? Disable TOR/Firewall on the phone!

            HttpClient httpclient = getNewHttpClient(); // custom client, that support SSL
            HttpResponse resp = null;

            if(!urlParameters.isEmpty())
            {
                HttpPost post = new HttpPost(uri);
//                post.setEntity(new UrlEncodedFormEntity(urlParameters));
                StringEntity params = new StringEntity(urlParameters);
                post.addHeader("Content-Type", "application/x-www-form-urlencoded");
                post.setEntity(params);

                resp = httpclient.execute(post);
            }else{
                resp = httpclient.execute(new HttpGet(uri));
            }

            StatusLine statusLine = resp.getStatusLine();
            if (statusLine.getStatusCode() == HttpStatus.SC_OK) {
                ByteArrayOutputStream out = new ByteArrayOutputStream();
                if(file != null)
                {
                    // write response to the file
                    BufferedOutputStream buffOut=new BufferedOutputStream(new FileOutputStream(file));
                    resp.getEntity().writeTo(buffOut);
                    buffOut.close();

                }else {
                    // save response to Page as a string
                    resp.getEntity().writeTo(out);
                    page = out.toString();
                }

                last_http_code = statusLine.getStatusCode();
                out.close();

            } else {
                // Closes the connection.
                resp.getEntity().getContent().close();
                // throw new IOException(statusLine.getReasonPhrase());
                last_http_code = statusLine.getStatusCode();
                error = "response code: " + String.valueOf(last_http_code);
            }

        } catch (Exception e) {
            StringWriter sw = new StringWriter();
            PrintWriter pw = new PrintWriter(sw);
            e.printStackTrace(pw);
            error = "request exception: " + sw.toString();
        }

        if(error != null) {
            Log.e("Tag", error);
            return null;
        }

        return page;
    }

    // File file = new File(Constants.DOWNLOADS_DIR, "TEST.apk");
    protected int getFile(String url, File file) {

        Object res = makeRequest(url, file);
        Settings.debug("NetworkTask.onPostExecute response code: " + String.valueOf(last_http_code));

        return last_http_code;
    }

    protected String exec(String url) {

        String page = makeRequest(url, null);
        Settings.debug("NetworkTask.onPostExecute response code: " + String.valueOf(last_http_code));

        if(page == null)
            Settings.debug("onPostExecute error: " + error);

        return page;
    }

    public HttpClient getNewHttpClient() {
        try {
            KeyStore trustStore = KeyStore.getInstance(KeyStore
                    .getDefaultType());
            trustStore.load(null, null);

            MySSLSocketFactory sf = new MySSLSocketFactory(trustStore);
            sf.setHostnameVerifier(SSLSocketFactory.ALLOW_ALL_HOSTNAME_VERIFIER);

            HttpParams params = new BasicHttpParams();
            HttpProtocolParams.setVersion(params, HttpVersion.HTTP_1_1);
            HttpProtocolParams.setContentCharset(params, HTTP.UTF_8);

            SchemeRegistry registry = new SchemeRegistry();
            registry.register(new Scheme("http", PlainSocketFactory
                    .getSocketFactory(), 80));
            registry.register(new Scheme("https", sf, 443));

            ClientConnectionManager ccm = new ThreadSafeClientConnManager(
                    params, registry);

            return new DefaultHttpClient(ccm, params);
        } catch (Exception e) {
            return new DefaultHttpClient();
        }
    }
} // end