using IR2S.Admin.Classes;
using IR2S.Common.Exceptions;
using IR2S.Admin.Helpers;
using IR2S.Admin.Models;
using NLog;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Threading;
using System.Web;
using System.Web.Mvc;
using IR2S.Common;

namespace IR2S.Admin.Controllers
{
    public class BaseController : Controller
    {
        protected static Logger iLogger;
        protected static Logger eLogger;
        public int[] allowedStores;

        private const string PARAMETER_NAME = "q=";
        private const string ENCRYPTION_KEY = "aSYA36Wr23wdf22";

        public BAEntities db = new BAEntities();

        protected virtual new CustomPrincipal User
        {
            get { return HttpContext.User as CustomPrincipal; }
        }

        protected override void OnActionExecuting(ActionExecutingContext filterContext)
        {
            if (HttpContext.Request.Cookies.AllKeys.Contains("timeZone"))
            {
                Session["TimeZone"] =
                    HttpContext.Request.Cookies["timeZone"].Value;
            }
            else
                Session["TimeZone"] = "0";

            iLogger = LogManager.GetLogger("Info");
            eLogger = LogManager.GetLogger("Errors");

            if (HttpContext.Request.RawUrl.Contains("?"))
            {
                string query = ExtractQuery(HttpContext.Request.RawUrl);
                string path = GetVirtualPath();

                if (query.StartsWith(PARAMETER_NAME, StringComparison.OrdinalIgnoreCase))
                {
                    // Decrypts the query string and rewrites the path.
                    string rawQuery = query.Replace(PARAMETER_NAME, string.Empty);
                    string decryptedQuery = Decrypt(rawQuery);
                    HttpContext.RewritePath(path, string.Empty, decryptedQuery);
                }
                else if (HttpContext.Request.HttpMethod == "GET")
                {
                    // Encrypt the query string and redirects to the encrypted URL. For auto encryption of all query string urls
                    //string encryptedQuery = Encrypt(query);
                    //HttpContext.Response.Redirect(path + encryptedQuery);
                }
            }

            base.OnActionExecuting(filterContext);
        }

        public void HandleException(Exception exception)
        {
            eLogger.Error(exception);
        }

        public void HandleException(Exception exception, dynamic model)
        {
            if (model != null && model is IR2S.Common.ModelMessage)
            {
                if (exception is BAException)
                {
                    model.DisplayMessage = true;
                    model.Message = ((BAException)exception).ErrorMessage;
                }
                else
                {
                    model.DisplayMessage = true;
                    model.Message = exception.Message;
                    // ---- Translate if possible
                }
            }

            eLogger.Error(exception);
        }

        protected override IAsyncResult BeginExecuteCore(AsyncCallback callback, object state)
        {
            string cultureName = null;

            // Attempt to read the culture cookie from Request
            HttpCookie cultureCookie = Request.Cookies["_culture"];
            if (cultureCookie != null)
                cultureName = cultureCookie.Value;
            else
                cultureName = Request.UserLanguages != null && Request.UserLanguages.Length > 0 ?
                        Request.UserLanguages[0] :  // obtain it from HTTP header AcceptLanguages
                        null;
            // Validate culture name
            cultureName = CultureHelper.GetImplementedCulture(cultureName); // This is safe

            #region Culture name in URL

            //string cultureName = RouteData.Values["culture"] as string;

            //// Attempt to read the culture cookie from Request
            //if (cultureName == null)
            //    cultureName = Request.UserLanguages != null && Request.UserLanguages.Length > 0 ? Request.UserLanguages[0] : null; // obtain it from HTTP header AcceptLanguages

            //// Validate culture name
            //cultureName = CultureHelper.GetImplementedCulture(cultureName); // This is safe


            //if (RouteData.Values["culture"] as string != cultureName)
            //{

            //    // Force a valid culture in the URL
            //    RouteData.Values["culture"] = cultureName.ToLowerInvariant(); // lower case too

            //    // Redirect user
            //    Response.RedirectToRoute(RouteData.Values);
            //}

            #endregion

            // Modify current thread's cultures            
            Thread.CurrentThread.CurrentCulture = new System.Globalization.CultureInfo(cultureName);
            Thread.CurrentThread.CurrentUICulture = Thread.CurrentThread.CurrentCulture;

            return base.BeginExecuteCore(callback, state);

        }

        #region Encryption/decryption

        /// <summary>
        /// Parses the current URL and extracts the virtual path without query string.
        /// </summary>
        /// <returns>The virtual path of the current URL.</returns>
        private string GetVirtualPath()
        {
            string path = HttpContext.Request.RawUrl;
            path = path.Substring(0, path.IndexOf("?"));
            path = path.Substring(path.LastIndexOf("/") + 1);
            return path;
        }

        /// <summary>
        /// Parses a URL and returns the query string.
        /// </summary>
        /// <param name="url">The URL to parse.</param>
        /// <returns>The query string without the question mark.</returns>
        private string ExtractQuery(string url)
        {
            int index = url.IndexOf("?") + 1;
            return url.Substring(index);
        }

        /// <summary>
        /// The salt value used to strengthen the encryption.
        /// </summary>
        private readonly static byte[] SALT = Encoding.ASCII.GetBytes(ENCRYPTION_KEY.Length.ToString());

        /// <summary>
        /// Encrypts any string using the Rijndael algorithm.
        /// </summary>
        /// <param name="inputText">The string to encrypt.</param>
        /// <returns>A Base64 encrypted string.</returns>
        public static string Encrypt(string inputText)
        {
            RijndaelManaged rijndaelCipher = new RijndaelManaged();
            byte[] plainText = Encoding.Unicode.GetBytes(inputText);
            PasswordDeriveBytes SecretKey = new PasswordDeriveBytes(ENCRYPTION_KEY, SALT);

            using (ICryptoTransform encryptor = rijndaelCipher.CreateEncryptor(SecretKey.GetBytes(32), SecretKey.GetBytes(16)))
            {
                using (MemoryStream memoryStream = new MemoryStream())
                {
                    using (CryptoStream cryptoStream = new CryptoStream(memoryStream, encryptor, CryptoStreamMode.Write))
                    {
                        cryptoStream.Write(plainText, 0, plainText.Length);
                        cryptoStream.FlushFinalBlock();
                        return "?" + PARAMETER_NAME + Convert.ToBase64String(memoryStream.ToArray());
                    }
                }
            }
        }

        /// <summary>
        /// Decrypts a previously encrypted string.
        /// </summary>
        /// <param name="inputText">The encrypted string to decrypt.</param>
        /// <returns>A decrypted string.</returns>
        public static string Decrypt(string inputText)
        {
            RijndaelManaged rijndaelCipher = new RijndaelManaged();
            byte[] encryptedData = Convert.FromBase64String(inputText);
            PasswordDeriveBytes secretKey = new PasswordDeriveBytes(ENCRYPTION_KEY, SALT);

            using (ICryptoTransform decryptor = rijndaelCipher.CreateDecryptor(secretKey.GetBytes(32), secretKey.GetBytes(16)))
            {
                using (MemoryStream memoryStream = new MemoryStream(encryptedData))
                {
                    using (CryptoStream cryptoStream = new CryptoStream(memoryStream, decryptor, CryptoStreamMode.Read))
                    {
                        byte[] plainText = new byte[encryptedData.Length];
                        int decryptedCount = cryptoStream.Read(plainText, 0, plainText.Length);
                        return Encoding.Unicode.GetString(plainText, 0, decryptedCount);
                    }
                }
            }
        }
        #endregion
    }
}