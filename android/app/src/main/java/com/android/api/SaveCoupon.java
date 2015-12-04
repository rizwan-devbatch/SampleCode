package com.android.api;

import android.content.Context;
import android.os.AsyncTask;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.protocol.HTTP;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
public class SaveCoupon extends AsyncTask<Void, Void, Void> {
    private boolean isStarted;
    private String consumerId;
    private String campain_id;
    private String couponid;
    private String campain_type;
    private Context context;

    public  SaveCoupon(Context context){
        this.context = context;
    }

    public String getConsumerId() {
        return consumerId;
    }

    public void setConsumerId(String consumerId) {
        this.consumerId = consumerId;
    }

    public String getCampain_id() {
        return campain_id;
    }

    public void setCampain_id(String campain_id) {
        this.campain_id = campain_id;
    }

    public String getCouponid() {
        return couponid;
    }

    public void setCouponid(String couponid) {
        this.couponid = couponid;
    }

    public String getCampain_type() {
        return campain_type;
    }

    public void setCampain_type(String campain_type) {
        this.campain_type = campain_type;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
      }

    @Override
    protected void onPostExecute(Void aVoid) {
        super.onPostExecute(aVoid);
        if (isStarted == true) {
        }
    }

    @Override
    protected Void doInBackground(Void... params) {
        isStarted = false;

        try {
            HttpClient client = new DefaultHttpClient();
            HttpConnectionParams.setConnectionTimeout(client.getParams(),
                    10000); // Timeout Limit
            HttpResponse response;
            JSONObject json = new JSONObject();
            HttpPost post = new HttpPost("http://apiv1.url_for_user");
            json.put("consumer_id",getConsumerId());
            json.put("campaign_id",getCampain_id());
            json.put("couponid",getCouponid());
            json.put("campaigntype",getCampain_type());
            StringEntity se = new StringEntity(json.toString());
            se.setContentType(new BasicHeader(HTTP.CONTENT_TYPE,
                    "application/json"));
            post.setEntity(se);
            response = client.execute(post);
            if (response != null) {
                System.out.println("LoadJobAsysncTask doInBackGround called " + response.toString());
                InputStream in = response.getEntity().getContent();
                BufferedReader r = new BufferedReader(
                        new InputStreamReader(in));
                StringBuilder total = new StringBuilder();
                String line;
                while ((line = r.readLine()) != null) {
                    total.append(line);
                }
                System.out.println("Save coupon "+total);
                final JSONObject obj = new JSONObject(total.toString());

                if (obj.getInt("status") == 1) {
                    isStarted = true;
                }
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
        // TODO Auto-generated method stub
        return null;
    }
}