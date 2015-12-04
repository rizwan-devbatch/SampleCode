package com.architecture.android;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.media.MediaPlayer;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.consumerbreak.api.AllCategories;
import com.consumerbreak.fragments.AdGameFragment;
import com.consumerbreak.fragments.CategorySearch;
import com.consumerbreak.fragments.CouponDetail;
import com.consumerbreak.fragments.CouponFragment;
import com.consumerbreak.fragments.GiftFragment;
import com.consumerbreak.fragments.HomeFragment;
import com.consumerbreak.fragments.ItemDetails;
import com.consumerbreak.fragments.MoreFragment;
import com.consumerbreak.fragments.PayMoneyFragment;
import com.consumerbreak.fragments.PointsFragment;
import com.consumerbreak.fragments.SearchFragment;
import com.consumerbreak.localdatabase.CBDataBaseManager;
import com.consumerbreak.localdatabase.CBDataManager;
import com.consumerbreak.localdbmodel.Filter;
import com.consumerbreak.utils.ApplicationConstants;
import com.consumerbreak.utils.GeneralSettings;
import com.google.android.gms.gcm.GoogleCloudMessaging;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import java.io.IOException;
import java.util.HashMap;
import java.util.NoSuchElementException;
import java.util.Stack;

/**
 * Created by DevBatch Pc on 12/1/2014.
 */
public class MainActivity extends FragmentActivity implements View.OnClickListener {
    private final String TAG = MainActivity.class.toString();
    private TextView textViewGiftCounter,textViewPrizeCounter;
    public String currentMenu, userId, url;
    private Fragment content;
    public static MainActivity context = null;
    private ImageView imageViewLocation, imageViewMore, imageViewAdGame, imageViewHome, imageViewSearchHome, imageViewDollar, imageViewScissor, imageViewShop, imageViewGift;
    private LinearLayout linearLayoutSearchIcon, linearLayoutHome, linearLayoutAdGame, linearLayoutLocation, linearLayoutMore,relativeLayoutFooter;
    private RelativeLayout relativeLayoutHeader;
    public HashMap<String, Stack<Fragment>> stacks;
    public HashMap<String, Fragment> allFragments;
    private boolean started, isPLAYING, isPause, isResume = false;
    public FragmentAnimation fragmentAnimation;
    private CBDataManager dataManager;
    private CBDataBaseManager dataBaseManager;
    MediaPlayer mp;
    public Filter filterUser = null;
    private int media_length;
    RequestParams params = new RequestParams();
    String regId = "";
    private  SharedPreferences sharedpreferences;

    private final static int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
    public static enum SliderMenu {
        HOME, SEARCH, LOCATION, MORE, PAYMENT, COUPONS, WALLET,GIFT,ADGAME;
        @Override
        public String toString() {
            return super.toString();
        }

    }

    public static enum FragmentAnimation {
        SLIDE_UP, SLIDE_DOWN, SLIDE_RIGHT, SLIDE_LEFT, NONE
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);
        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.main_activity);
        Bundle extras = getIntent().getExtras();
        if (extras != null) {
            userId = extras.getString("USER_ID");
        }
        initSingleton(true);
        setTypeFace();
    }

    public void stopPlaying() {
        if (mp != null) {
            mp.release();
            mp = null;
        }
        isPLAYING = false;
    }

    public void onMusicPlayerImageButton(String url) {
        if (!isPLAYING) {
            this.url = url;
            isPLAYING = true;
            isPause = false;
            new RunMusic().execute();
        } else if (!isPause) {
            isResume = true;
            isPause = true;
            mp.pause();
            media_length = mp.getCurrentPosition();
        } else if (isResume) {
            isResume = false;
            isPause = false;
            mp.seekTo(media_length);
            mp.start();
            mp.setLooping(true);
        }
    }

    public boolean isMediaPlayerPlaying() {
        if (mp != null && mp.isPlaying()) {
            return true;
        } else {
            return false;
        }
    }

    public void initSingleton(boolean replace) {
        if (replace) {
            setDataManager(CBDataManager.reinitializeORMLiteDataManager(this));
        } else {
            setDataManager(CBDataManager.getToastDataManager(this));
        }
        if (dataBaseManager == null) {
            setDataBaseManager(dataManager.getDatabaseManager());
        }
    }

    public void setDataManager(CBDataManager dataManager) {
        this.dataManager = dataManager;
    }


    public CBDataManager getDataManager() {
        return dataManager;
    }

    public CBDataBaseManager getDataBaseManager() {
        return dataBaseManager;
    }

    public void setDataBaseManager(CBDataBaseManager dataBaseManager) {
        this.dataBaseManager = dataBaseManager;
    }

    private void initializeViewsAndSetTypeFace() {
        textViewGiftCounter = (TextView) findViewById(R.id.notification_gift);
        textViewPrizeCounter = (TextView) findViewById(R.id.notification_prize);
        sharedpreferences = getSharedPreferences(ApplicationConstants.NOTIFICATIONPREFERENCES, Context.MODE_PRIVATE);
        int gift_counter = sharedpreferences.getInt(ApplicationConstants.USER_ID+ApplicationConstants.Gift, 0);
        int prize_counter = sharedpreferences.getInt(ApplicationConstants.USER_ID+ApplicationConstants.Prize, 0);
        if(gift_counter > 0) {
            textViewGiftCounter.setText("" + gift_counter + "");
            textViewGiftCounter.setVisibility(View.VISIBLE);
        }
        else
            textViewGiftCounter.setVisibility(View.GONE);
        if(prize_counter > 0) {
            textViewPrizeCounter.setText("" + prize_counter + "");
            textViewPrizeCounter.setVisibility(View.VISIBLE);
        }
        else
            textViewPrizeCounter.setVisibility(View.GONE);
        relativeLayoutFooter = (LinearLayout) findViewById(R.id.relativeLayoutFooter);
       
        linearLayoutHome = (LinearLayout) findViewById(R.id.linearLayoutHome);
        linearLayoutAdGame = (LinearLayout) findViewById(R.id.linearLayoutAdGame);
        linearLayoutLocation = (LinearLayout) findViewById(R.id.linearLayoutLocation);
        linearLayoutMore = (LinearLayout) findViewById(R.id.linearLayoutMore);
        linearLayoutMore.setOnClickListener(this);
        imageViewAdGame.setOnClickListener(this);
        imageViewDollar.setOnClickListener(this);
        imageViewLocation.setOnClickListener(MainActivity.this);
        imageViewMore.setOnClickListener(this);
    }

    @Override
    public void onClick(View v) {
        stopPlaying();
        linearLayoutHome.setBackgroundColor(getResources().getColor(android.R.color.transparent));
        linearLayoutAdGame.setBackgroundColor(getResources().getColor(android.R.color.transparent));
        linearLayoutLocation.setBackgroundColor(getResources().getColor(android.R.color.transparent));
        linearLayoutSearchIcon.setBackgroundColor(getResources().getColor(android.R.color.transparent));
        linearLayoutMore.setBackgroundColor(getResources().getColor(android.R.color.transparent));

        imageViewGift.setBackgroundResource(R.drawable.main_icon_background);
        imageViewShop.setBackgroundResource(R.drawable.main_icon_background);
        imageViewDollar.setBackgroundResource(R.drawable.main_icon_background);
        imageViewScissor.setBackgroundResource(R.drawable.main_icon_background);

        sharedpreferences = getSharedPreferences(ApplicationConstants.NOTIFICATIONPREFERENCES, Context.MODE_PRIVATE);
        int gift_counter = sharedpreferences.getInt(ApplicationConstants.USER_ID+ApplicationConstants.Gift, 0);
        int prize_counter = sharedpreferences.getInt(ApplicationConstants.USER_ID+ApplicationConstants.Prize, 0);
        if(gift_counter > 0) {
            textViewGiftCounter.setText("" + gift_counter + "");
            textViewGiftCounter.setVisibility(View.VISIBLE);
        }
        else
            textViewGiftCounter.setVisibility(View.GONE);
        if(prize_counter > 0) {
            textViewPrizeCounter.setText("" + prize_counter + "");
            textViewPrizeCounter.setVisibility(View.VISIBLE);
        }
        else
            textViewPrizeCounter.setVisibility(View.GONE);

        switch (v.getId()) {
            case R.id.imageViewLocation:
                stacks.get(currentMenu).clear();
                currentMenu = SliderMenu.LOCATION.toString();
                stacks.get(currentMenu).clear();
                linearLayoutLocation.setBackgroundColor(getResources().getColor(android.R.color.white));
                linearLayoutLocation.getBackground().setAlpha(35);
                switchContent(currentMenu, SearchFragment.newInstance(),
                        true, MainActivity.FragmentAnimation.SLIDE_UP);
                break;
           
            case R.id.imageViewSearchHome:
                stacks.get(currentMenu).clear();
                currentMenu = SliderMenu.SEARCH.toString();
                stacks.get(currentMenu).clear();
                linearLayoutSearchIcon.setBackgroundColor(getResources().getColor(android.R.color.white));
                linearLayoutSearchIcon.getBackground().setAlpha(35);
                switchContent(currentMenu, CategorySearch.newInstance(),
                        true, FragmentAnimation.SLIDE_LEFT);
                break;
          
            case R.id.imageViewShop:
                imageViewShop.setBackgroundResource(R.drawable.main_icon_background_selected);
                if(currentMenu.equals("SEARCH"))
                    return;
                stacks.get(currentMenu).clear();
                currentMenu = SliderMenu.SEARCH.toString();
                stacks.get(currentMenu).clear();
                switchContent(currentMenu, PointsFragment.newInstance(),
                        true, FragmentAnimation.SLIDE_UP);
                break;
            case R.id.linearLayoutSearchIcon:
                stacks.get(currentMenu).clear();
                currentMenu = SliderMenu.SEARCH.toString();
                stacks.get(currentMenu).clear();
                linearLayoutSearchIcon.setBackgroundColor(getResources().getColor(android.R.color.white));
                linearLayoutSearchIcon.getBackground().setAlpha(35);
                switchContent(currentMenu, CategorySearch.newInstance(),
                        true, FragmentAnimation.SLIDE_LEFT);
                break;
            case R.id.linearLayoutHome:
                HomeFragment.headerVisibility="invisible";
                stacks.get(currentMenu).clear();
                currentMenu = SliderMenu.HOME.toString();
                stacks.get(currentMenu).clear();
                linearLayoutHome.setBackgroundColor(getResources().getColor(android.R.color.white));
                linearLayoutHome.getBackground().setAlpha(35);
                switchContent(currentMenu, HomeFragment.newInstance(),
                        true, FragmentAnimation.SLIDE_LEFT);
                break;
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        if (started) {
            return;
        }
        startApp();

    }

    @Override
    protected void onPause() {
        super.onPause();
        stopPlaying();
	}
    @Override
    public void onBackPressed() {
        Fragment frag;
        try {
            frag = stacks.get(currentMenu).lastElement();
        } catch (NoSuchElementException nse) {
            Log.e(TAG, nse.toString(), nse);
            return;
        }
        if (frag instanceof HomeFragment) {
            ((HomeFragment) frag).onBackPressed();
        } else if (frag instanceof MoreFragment) {
            ((MoreFragment) frag).onBackPressed();
        } else if (frag instanceof SearchFragment) {
            ((SearchFragment) frag).onBackPressed();
        } else if (frag instanceof AdGameFragment) {
            ((AdGameFragment) frag).onBackPressed();
        } else if (frag instanceof CategorySearch) {
            ((CategorySearch) frag).onBackPressed();
        } else if (frag instanceof CouponFragment) {
            ((CouponFragment) frag).onBackPressed();
        } else if (frag instanceof PayMoneyFragment) {
            ((PayMoneyFragment) frag).onBackPressed();
        } else if (frag instanceof CouponDetail) {
            ((CouponDetail) frag).onBackPressed();
        } else if (frag instanceof PointsFragment) {
            ((PointsFragment) frag).onBackPressed();
        } else if (frag instanceof ItemDetails) {
            ((ItemDetails) frag).onBackPressed();
        }
    }

    @Override
    protected void onDestroy() {
        // TODO Auto-generated method stub
        super.onDestroy();
    }
    public void startApp() {
        started = true;
        context = MainActivity.this;
      
        stacks = new HashMap<String, Stack<Fragment>>();

        for (SliderMenu menu : SliderMenu.values()) {
            stacks.put(menu.toString(), new Stack<Fragment>());
        }
        HomeFragment homeFragment = HomeFragment.newInstance();
        allFragments.put(SliderMenu.HOME.toString()
                + HomeFragment.class.getName().toString(), homeFragment);
        int switchToFragment = 1;
        switch (switchToFragment) {
            case 1:
                currentMenu = SliderMenu.HOME.toString();
                content = allFragments.get(SliderMenu.HOME.toString()
                        + HomeFragment.class.getName().toString());
                break;
            case 2:
                currentMenu = SliderMenu.GIFT.toString();
                content = allFragments.get(SliderMenu.GIFT.toString()
                        + GiftFragment.class.getName().toString());
                break;
            case 3:
                currentMenu = SliderMenu.PAYMENT.toString();
                content = allFragments.get(SliderMenu.PAYMENT.toString()
                        + PayMoneyFragment.class.getName().toString());
                break;
            default:
                break;
        }
        stacks.get(currentMenu).push(content);
        setContentView(R.layout.main_activity);
        getSupportFragmentManager().beginTransaction()
                .replace(R.id.content_frame, content).commit();
        initializeViewsAndSetTypeFace();
        linearLayoutHome.setBackgroundColor(getResources().getColor(android.R.color.white));
        linearLayoutHome.getBackground().setAlpha(35);
    }

    public int stackSize() {
        if (stacks.get(currentMenu) != null) {
            return stacks.get(currentMenu).size();
        }
        return 0;
    }


    /**
     * @param tag       the stack key
     * @param fragment  the fragment you want to push
     * @param shouldAdd if true then fragment add on stack
     * @param anim      if 1 fragment animation left to right if 2 fragment animation
     *                  right to left
     */
    private void pushFragments(String tag, Fragment fragment,
                               boolean shouldAdd, FragmentAnimation anim) {
        if (shouldAdd) {
            stacks.get(tag).push(fragment);
        }
        try {
            FragmentManager manager = getSupportFragmentManager();
            FragmentTransaction ft = manager.beginTransaction();
            Fragment oldFrag2 = manager.findFragmentById(R.id.content_frame);

            Log.e("Frag","tag is "+tag+" frag tag "+fragment.getTag()+" frag acitivy is "+fragment.toString()+" frag id is "+fragment.getId() +" old frag is "+oldFrag2.toString());
            if (anim == FragmentAnimation.SLIDE_LEFT) {
                ft.setCustomAnimations(R.anim.slide_in_right,
                        R.anim.slide_out_left, R.anim.slide_in_right,
                        R.anim.slide_out_left);
                fragmentAnimation = FragmentAnimation.SLIDE_RIGHT;
            }
            if (anim == FragmentAnimation.SLIDE_RIGHT) {
                ft.setCustomAnimations(R.anim.slide_in_left,
                        R.anim.slide_out_right, R.anim.slide_in_left,
                        R.anim.slide_out_right);
            }
            if (anim == FragmentAnimation.SLIDE_UP) {
                ft.setCustomAnimations(R.anim.push_up_in, R.anim.push_up_out,
                        R.anim.push_up_in, R.anim.push_up_out);
                fragmentAnimation = FragmentAnimation.SLIDE_DOWN;
            }
            getSupportFragmentManager().beginTransaction()
                    .replace(R.id.content_frame, fragment).commit();

        } catch (Exception e) {
            Log.e(TAG, "pushFragment() error", e);
        }

    }

    public void popFragments() {
        /*
		 * Select the second last fragment in current tab's stack.. which will
		 * be shown after the fragment transaction given below
		 */
        Fragment fragment = stacks.get(currentMenu).elementAt(
                stacks.get(currentMenu).size() - 2);
        // Fragment fragment = stacks.get(currentMenu).lastElement();
		/* pop current fragment from stack.. */
        stacks.get(currentMenu).pop();
		/*
		 * We have the target fragment in hand.. Just show it.. Show a standard
		 * navigation animation
		 */
        FragmentManager manager = getSupportFragmentManager();
        FragmentTransaction ft = manager.beginTransaction();

		/*
		 * int enter, int exit, int popEnter, int popExit
		 */
        if (fragmentAnimation == FragmentAnimation.SLIDE_DOWN) {
            ft.setCustomAnimations(R.anim.push_down_in, R.anim.push_down_out,
                    R.anim.push_down_in, R.anim.push_down_out);
        } else {
            ft.setCustomAnimations(R.anim.slide_in_left,
                    R.anim.slide_out_right, R.anim.slide_in_left,
                    R.anim.slide_out_right);
        }

        Fragment oldFrag = manager.findFragmentById(R.id.content_frame);
        if (oldFrag != null) {
            ft.remove(oldFrag);
        }

        ft.add(R.id.content_frame, fragment);
        ft.commit();
    }

    public void switchContent(String tag, Fragment fragment, boolean shouldAdd,
                              FragmentAnimation anim) {
        content = fragment;
        pushFragments(tag, fragment, shouldAdd, anim);
        relativeLayoutFooter.setVisibility(View.VISIBLE);
        relativeLayoutHeader.setVisibility(View.VISIBLE);
    }
    public void ViewTheHeaderAndFooter(){
        relativeLayoutFooter.setVisibility(View.VISIBLE);
        relativeLayoutHeader.setVisibility(View.VISIBLE);
    }
    public void switchContent(String hoverChange,String tag, Fragment fragment, boolean shouldAdd,
                              FragmentAnimation anim) {
        relativeLayoutFooter.setVisibility(View.VISIBLE);
        relativeLayoutHeader.setVisibility(View.VISIBLE);
        if(hoverChange.equals("FromSearchToHome")){
            linearLayoutLocation.getBackground().setAlpha(0);
            linearLayoutSearchIcon.getBackground().setAlpha(0);
            linearLayoutHome.setBackgroundColor(getResources().getColor(android.R.color.white));
            linearLayoutHome.getBackground().setAlpha(35);
        }
        else if(hoverChange.equals("NoBottomNavigation")){
            linearLayoutHome.setBackgroundColor(getResources().getColor(android.R.color.transparent));
            linearLayoutAdGame.setBackgroundColor(getResources().getColor(android.R.color.transparent));
            linearLayoutLocation.setBackgroundColor(getResources().getColor(android.R.color.transparent));
            linearLayoutSearchIcon.setBackgroundColor(getResources().getColor(android.R.color.transparent));
            linearLayoutMore.setBackgroundColor(getResources().getColor(android.R.color.transparent));
        }
        content = fragment;
        pushFragments(tag, fragment, shouldAdd, anim);

    }
    public void switchContent(boolean hideHeaderAndFooter,String tag, Fragment fragment, boolean shouldAdd,
                              FragmentAnimation anim) {
        if(hideHeaderAndFooter){
            relativeLayoutFooter.setVisibility(View.GONE);
            relativeLayoutHeader.setVisibility(View.GONE);
        }
        else
        {
            relativeLayoutFooter.setVisibility(View.VISIBLE);
            relativeLayoutHeader.setVisibility(View.VISIBLE);
        }
        content = fragment;
        pushFragments(tag, fragment, shouldAdd, anim);
    }

    public final Fragment getCurrentFragment() {
        Fragment frag = null;
        FragmentManager manager = getSupportFragmentManager();
        frag = manager.findFragmentById(R.id.content_frame);
        return frag;
    }

    private void setTypeFace() {
        Typeface typefaceNeoSans = Typeface.createFromAsset(
                getAssets(), "Neo_Sans.ttf");
        Typeface typefaceNeoSansLight = Typeface.createFromAsset(
                getAssets(), "Neo_Sans_Light.ttf");
        GeneralSettings.getInstanse().setContext(this);
        GeneralSettings.getInstanse().setSansNeoLight(typefaceNeoSansLight);
        GeneralSettings.getInstanse().setSansNeo(typefaceNeoSans);
    }

    private class RunMusic extends AsyncTask<Void, Boolean, Boolean> {
        private ProgressDialog dialog;
        private boolean isStarted;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }
        @Override
        protected Boolean doInBackground(Void... params) {
            try {
                mp = new MediaPlayer();
                mp.setDataSource(url);
                mp.prepare();
                mp.setLooping(true);
                mp.start();

            }catch (IllegalStateException exception){
            } catch (IOException e) {
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

        }
    }
   
    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        setIntent(intent);
    }
}
