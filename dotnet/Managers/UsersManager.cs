using IR2S.Common.Enums;
using IR2S.Common.Exceptions;
using IR2S.Admin.Models;
using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Web;
using IR2S.Admin.Classes;
using IR2S.Common;

namespace IR2S.Admin.Managers
{
    public class UsersManager : BaseManager
    {
        public IEnumerable<UserCredential> GetUsers(int roleID)
        {
            var ApprovalStatus = (int)eApprovalStatus.Approved;

            var roleIds = db.Roles.Find(roleID).ChildRoles;

            List<int> roles = new List<int>();
            if (roleIds != "" && roleIds != null)
                foreach (var role in roleIds.Split(','))
                {
                    roles.Add(Convert.ToInt32(role));
                }
            roles.Add(roleID);

            var query = db.UserCredentials.Where(s => s.UserInfo.ApprovalStatus == ApprovalStatus
                && roles.Contains(s.RoleID)).OrderBy(u => u.UserInfo.ID);

            return query;
        }

        public bool AddUser(UserViewModel model, int userID)
        {
            var checkLoginIDs = db.UserCredentials.Where(u => u.LoginID == model.userCredentials.LoginID.Trim() && u.UserInfo.IsActive).Count();
            var checkEmailAddressAvailability = db.UserCredentials.Where(u => u.EmailAddress == model.userCredentials.EmailAddress.Trim() && u.UserInfo.IsActive).Count();

            if (checkLoginIDs >= 1)
            {
                throw new BAException(eErrorCode.DuplicateLoginID);
            }
            else if (checkEmailAddressAvailability >= 1)
            {
                throw new BAException(eErrorCode.DuplicateEmail);
            }

            using (var trans = new System.Transactions.TransactionScope())
            {
                model.userCredentials.Password = Encrypt(model.PASSWORD);

                model.userInfo.UpdatedOn = System.DateTime.UtcNow;
                model.userInfo.CreatedBy = userID;
                model.userInfo.UpdatedBy = userID;
                model.userInfo.UserSince = DateTime.UtcNow;
                model.userInfo.Picture = Convert.ToBase64String(model.Image);
                model.userInfo.ApprovalStatus = (int)eApprovalStatus.Approved;
                model.userInfo.AccountType = (int)eAccountType.Admin;
                db.UserInfoes.Add(model.userInfo);
                db.SaveChanges();

                model.userCredentials.UserID = model.userInfo.ID;
                db.UserCredentials.Add(model.userCredentials);
                db.SaveChanges();

                // ---- Save Countries
                foreach (var row in model.CountryID)
                {
                    var userCountry = new UserCountry
                    {
                        ID = row,
                        UserID = model.userInfo.ID,
                        CountryID = row
                    };
                    db.UserCountries.Add(userCountry);
                    db.SaveChanges();
                };

                trans.Complete();
                return true;
            }
        }

        public bool EditUser(UserViewModel model, int userID)
        {
            var checkEmailAddressAvailability = db.UserCredentials.Where(u => u.EmailAddress == model.userCredentials.EmailAddress.Trim() && u.UserInfo.IsActive).Count();

            if (checkEmailAddressAvailability >= 1 && model.userInfo.ID == 0)
            {
                throw new BAException(eErrorCode.DuplicateEmail);
            }
            using (var trans = new System.Transactions.TransactionScope())
            {
                var editedUser = db.UserInfoes.Find(model.userInfo.ID);
                editedUser.FirstName = model.userInfo.FirstName;
                editedUser.LastName = model.userInfo.LastName;
                editedUser.Phone = model.userInfo.Phone;

                editedUser.Address = model.userInfo.Address;
                editedUser.Designation = model.userInfo.Designation;
                editedUser.IsActive = model.userInfo.IsActive;
                editedUser.Picture = Convert.ToBase64String(model.Image);
                editedUser.UpdatedBy = userID;
                editedUser.UpdatedOn = DateTime.UtcNow;
                db.Entry(editedUser).State = EntityState.Modified;
                db.SaveChanges();

                var userCred = db.UserCredentials.Where(p => p.UserID == model.userInfo.ID).First();
                userCred.Password = Encrypt(model.PASSWORD);
                userCred.EmailAddress = model.userCredentials.EmailAddress;
                userCred.UserID = model.userInfo.ID;
                userCred.RoleID = model.userCredentials.RoleID;
                db.Entry(userCred).State = EntityState.Modified;
                db.SaveChanges();

                // ---- Delete and Save Countries
                db.UserCountries.RemoveRange(db.UserCountries.Where(c => c.UserID == model.userInfo.ID));
                db.SaveChanges();

                foreach (var row in model.CountryID)
                {
                    var userCountry = new UserCountry
                    {
                        ID = row,
                        UserID = model.userInfo.ID,
                        CountryID = row
                    };
                    db.UserCountries.Add(userCountry);
                    db.SaveChanges();
                };

                trans.Complete();
                return true;
            }
        }


        public bool EditSellerProfile(SellerViewModel model)
        {
            using (var trans = new System.Transactions.TransactionScope())
            {
                var editedSeller = db.Sellers.Find(model.seller.ID);
                editedSeller.DisplayName = model.seller.DisplayName;

                editedSeller.CellNumber = model.seller.CellNumber;

                editedSeller.ZipCode = model.seller.ZipCode;
                editedSeller.City = model.seller.City;
                editedSeller.State = model.seller.State;
                editedSeller.Address1 = model.seller.Address1;
                editedSeller.Address2 = model.seller.Address2;
                editedSeller.Logo = Convert.ToBase64String(model.Image);
                db.Entry(editedSeller).State = EntityState.Modified;
                db.SaveChanges();


                var userCard = (model.cardInfo.ID != 0) ? db.CardInfoes.Find(model.cardInfo.ID) : new CardInfo();
                userCard.HolderName = model.cardInfo.HolderName;
                userCard.CardNumber = model.cardInfo.CardNumber;
                userCard.ExpiryMonth = model.cardInfo.ExpiryMonth;
                userCard.ExpiryYear = model.cardInfo.ExpiryYear;
                if (model.cardInfo.ID == 0)
                {
                    userCard.SellerID = editedSeller.ID;
                    db.CardInfoes.Add(userCard);
                }
                else
                {
                    db.Entry(userCard).State = EntityState.Modified;
                }
                db.SaveChanges();

                // ---- Delete and Save Countries

                trans.Complete();
                return true;
            }
        }

        public bool ChangePassword(ResetPasswordViewModel model)
        {
            UserCredential userCredentials = db.UserCredentials.Find(model.ID);

            userCredentials.Password = Encrypt(model.Password);
            db.Entry(userCredentials).State = EntityState.Modified;
            db.SaveChanges();
            return true;
        }

        public bool MarkInactive(int id)
        {
            // ---- Mark Inactive
            var user = db.UserInfoes.Find(id);

            user.IsActive = false;
            db.Entry(user).State = EntityState.Modified;
            db.SaveChanges();
            return true;
        }

        #region Helpers
        public List<Role> AllowedRoles(CustomPrincipal User)
        {
            var allowedRoles = new List<int>();
            allowedRoles.Add(User.ROLEID);

            var childRoles = db.Roles.Where(r => r.ID == User.ROLEID).First().ChildRoles;
            if (childRoles != null && childRoles != "")
            {
                foreach (var row in childRoles.Split(',').ToArray())
                {
                    allowedRoles.Add(Convert.ToInt32(row));
                }
            }

            if (User.ROLE == "ADMIN")
                return db.Roles.Where(r => r.RoleType != "SELLER").OrderBy(c => c.FullName).ToList();
            else
                return db.Roles.Where(r => allowedRoles.Contains(r.ID) && r.RoleType != "SELLER").OrderBy(s => s.FullName).ToList();
        }

        public List<Country> AllowedCountries(CustomPrincipal User)
        {
            var allowedCountries = new List<int>();

            if (User.ROLE == "ADMIN")
                return db.Countries.OrderBy(c => c.FullName).ToList();
            else
            {
                var countries = db.UserCountries.Where(r => r.UserID == User.USERID);
                if (countries.Count() != 0)
                {
                    foreach (var row in countries)
                    {
                        allowedCountries.Add(row.CountryID);
                    }
                }

                return db.Countries.Where(r => allowedCountries.Contains(r.ID)).OrderBy(s => s.FullName).ToList();
            }
        }

        #endregion
    }
}