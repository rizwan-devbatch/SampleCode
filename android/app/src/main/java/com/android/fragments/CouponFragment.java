package com.android.fragments;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.LinearLayout;
import android.widget.ListView;
import com.cig.consumerbreak.MainActivity;
import com.cig.consumerbreak.R;
import com.consumerbreak.adapter.CouponAdapter;
import com.consumerbreak.localdbmodel.Campain;
import com.consumerbreak.models.Coupon;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.protocol.HTTP;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

public class CouponFragment  extends BaseFragment {
    private View view;
    private ListView listViewCoupon;
    private CouponAdapter couponAdapter;
    private List<Coupon> listcoupon;
    private LinearLayout linearLayoutEmptyView;
    public static CouponFragment newInstance() {
        CouponFragment couponFragment;
        couponFragment = new CouponFragment();
        return couponFragment;
    }
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setRetainInstance(true);
    }
    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        super.onActivityCreated(savedInstanceState);
        initializeViews();
    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        if (view == null) {
            view = inflater.inflate(R.layout.coupon, container, false);
        } else {
            ViewGroup parent = (ViewGroup) view.getParent();
            if (parent != null) {
                parent.removeView(view);
            }
        }
        return view;
    }
    private void initializeViews() {
        linearLayoutEmptyView = (LinearLayout) view.findViewById(R.id.emptyView);
        listViewCoupon = (ListView) view.findViewById(android.R.id.list);
        listcoupon = new ArrayList<Coupon>();

    }
    @Override
    public void onResume() {
        super.onResume();
        new GetAllCoupons().execute();
    }
    @Override
    public void onBackPressed() {
        super.onBackPressed();
        if (getParent().stackSize() > 1) {
            getParent().popFragments();
        } else {
            getParent().finish();
        }
    }
    private class GetAllCoupons extends AsyncTask<Void, Boolean, Boolean> {
        private ProgressDialog dialog;
        private boolean isStarted;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            if (dialog == null) {
                dialog = new ProgressDialog(getParent());
                dialog.setIcon(null);
                dialog.setTitle("Loading");
                dialog.setMessage("Loading Coupons");
                dialog.show();
            }
        }
        @Override
        protected Boolean doInBackground(Void... params) {
            try {
                    HttpClient client = new DefaultHttpClient();
                    HttpConnectionParams.setConnectionTimeout(client.getParams(),
                            10000); // Timeout Limit
                    HttpResponse response;
                    JSONObject json = new JSONObject();
                    HttpPost post = new HttpPost("http://apiv1.arhitecture_android");
                    try {
                        json.put("consumer_id", getParent().userId);
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
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
                        final JSONObject obj = new JSONObject(total.toString());
                        if (obj.getInt("status") == 1) {
                            JSONArray couponJsonArray = obj.getJSONObject("result").getJSONArray("coupons");
                            for(int i = 0; i<couponJsonArray.length();i++) {
                                JSONObject couponJsonObject = (JSONObject) couponJsonArray.get(i);
                                if (couponJsonObject.getString("consumed").equals("0")) {
                                    Campain campain = getParent().getDataBaseManager().getCampaignByCampaignId(couponJsonObject.getString("campaign_id"));
                                    listcoupon.add(new Coupon(couponJsonObject.getString("consumer_id"), couponJsonObject.getString("campaign_id"), couponJsonObject.getString("consumed"), couponJsonObject.getString("couponid"), couponJsonObject.getString("coupon_level"), couponJsonObject.getString("image"),campain,couponJsonObject.optString("coupon_title"),couponJsonObject.optString("coupon_description")));
                                }
                              }
                            isStarted = true;
                        }
                    }
                } catch (Exception e) {
                e.printStackTrace();
            }
            return isStarted;
        }

        @Override
        protected void onProgressUpdate(Boolean... values) {
            super.onProgressUpdate(values);
        }

        @Override
        protected void onPostExecute(Boolean result) {
            super.onPostExecute(result);
                dialog.dismiss();
            if(listcoupon.size()<=0)
                linearLayoutEmptyView.setVisibility(View.VISIBLE);
            if(couponAdapter == null){
                couponAdapter = new CouponAdapter(getParent(),R.layout.coupon,0,listcoupon,CouponFragment.this);
            }
            if(isStarted == true && couponAdapter != null)
            listViewCoupon.setAdapter(couponAdapter);
            }
        }
}
