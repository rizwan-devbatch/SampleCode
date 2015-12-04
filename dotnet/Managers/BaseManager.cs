using IR2S.Admin.Models;
using IR2S.Common;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net.Mail;
using System.Security.Cryptography;
using System.Text;
using System.Web;

namespace IR2S.Admin.Managers
{
    public abstract class BaseManager
    {
        public BAEntities db = new BAEntities();

        /// <summary>
        /// Encrypt the given string mostly use for password encryption
        /// </summary>
        /// <param name="clearText"></param>
        /// <returns>
        /// Encrypted password.
        /// </returns>
        public string Encrypt(string clearText)
        {
            string EncryptionKey = "DBV2BAMVE37618";
            byte[] clearBytes = Encoding.Unicode.GetBytes(clearText);
            using (Aes encryptor = Aes.Create())
            {
                Rfc2898DeriveBytes pdb = new Rfc2898DeriveBytes(EncryptionKey, new byte[] { 0x49, 0x76, 0x61, 0x6e, 0x20, 0x4d, 0x65, 0x64, 0x76, 0x65, 0x64, 0x65, 0x76 });
                encryptor.Key = pdb.GetBytes(32);
                encryptor.IV = pdb.GetBytes(16);
                using (MemoryStream ms = new MemoryStream())
                {
                    using (CryptoStream cs = new CryptoStream(ms, encryptor.CreateEncryptor(), CryptoStreamMode.Write))
                    {
                        cs.Write(clearBytes, 0, clearBytes.Length);
                        cs.Close();
                    }
                    clearText = Convert.ToBase64String(ms.ToArray());
                }
            }
            return clearText;
        }

        /// <summary>
        /// Decrypts an encrypted string mostly use for password decryption.
        /// </summary>
        /// <param name="cipherText"></param>
        /// <returns>derypted string</returns>
        public string Decrypt(string cipherText)
        {
            string EncryptionKey = "DBV2BAMVE37618";
            byte[] cipherBytes = GetBytes(cipherText);
            using (Aes encryptor = Aes.Create())
            {
                Rfc2898DeriveBytes pdb = new Rfc2898DeriveBytes(EncryptionKey, new byte[] { 0x49, 0x76, 0x61, 0x6e, 0x20, 0x4d, 0x65, 0x64, 0x76, 0x65, 0x64, 0x65, 0x76 });
                encryptor.Key = pdb.GetBytes(32);
                encryptor.IV = pdb.GetBytes(16);
                using (MemoryStream ms = new MemoryStream())
                {
                    try
                    {
                        using (CryptoStream cs = new CryptoStream(ms, encryptor.CreateDecryptor(), CryptoStreamMode.Write))
                        {
                            cs.Write(cipherBytes, 0, cipherBytes.Length);
                        }
                        cipherText = Encoding.Unicode.GetString(ms.ToArray());
                    }
                    catch (Exception e)
                    {
                        Console.WriteLine("Error " + e);
                    }
                }
            }
            return cipherText;
        }

        static byte[] GetBytes(string str)
        {
            byte[] bytes = new byte[str.Length * sizeof(char)];
            System.Buffer.BlockCopy(str.ToCharArray(), 0, bytes, 0, bytes.Length);
            return bytes;
        }

        public void SendMessage(string toAddress, string subjectText, string bodyText)
        {
            var gmail_address = "abc.xyz@gmail.com";

            MailMessage newMessage = new MailMessage();

            MailAddress senderAddress = new MailAddress(gmail_address);
            MailAddress recipentAddress = new MailAddress(toAddress);

            bodyText = bodyText.Replace("\n", Environment.NewLine);

            newMessage.To.Add(recipentAddress);
            newMessage.From = senderAddress;
            newMessage.Subject = subjectText;
            newMessage.Body = bodyText;
            newMessage.IsBodyHtml = true;
            SmtpClient client = new SmtpClient();

            client.Send(newMessage);
        }

    }
}