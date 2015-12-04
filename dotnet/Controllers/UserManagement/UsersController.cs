using IR2S.Admin.Managers;
using Kendo.Mvc.UI;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using Kendo.Mvc.Extensions;
using IR2S.Admin.Models;
using IR2S.Admin.Classes;
using IR2S.Common.Enums;
using IR2S.Common;

namespace IR2S.Admin.Controllers.UserManagement
{
    [Authorize]
    public class UsersController : BaseController
    {
        private UsersManager _manager;
        
        public UsersController()
        {
            _manager = new UsersManager();
        }

        [RoleSecurity(Permissions.Users, PermissionType.VIEW)]
        public ActionResult Index()
        {
            return View(_manager.GetUsers(User.ROLEID));
        }

        [RoleSecurity(Permissions.Users, PermissionType.ADD)]
        public ActionResult Add()
        {
            ViewBag.RoleList = new SelectList(_manager.AllowedRoles(User), "ID", "FullName");
            ViewBag.CountriesList = new SelectList(_manager.AllowedCountries(User), "ID", "FullName");

            return View(new UserViewModel { Image = Convert.FromBase64String(DefaultPic) });
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Add(UserViewModel model)
        {
            try
            {
                if (ModelState.IsValid)
                {
                    if (_manager.AddUser(model, User.ID))
                    {
                        return RedirectToAction("Index");
                    }
                }
            }
            catch (Exception ex)
            {
                HandleException(ex, model);
            }

            ViewBag.RoleList = new SelectList(_manager.AllowedRoles(User), "ID", "FullName");
            ViewBag.CountriesList = new SelectList(_manager.AllowedCountries(User), "ID", "FullName");

            return View(model);
        }

        [RoleSecurity(Permissions.Users, PermissionType.EDIT)]
        public ActionResult Edit(int id = 0)
        {
            var userCredentials = db.UserCredentials.Find(id);
            if (userCredentials == null)
                return HttpNotFound();

            var user = db.UserInfoes.Find(userCredentials.UserID);

            UserViewModel model = new UserViewModel
            {
                UserCredentials = userCredentials,
                UserInfo = user,
                CountryID = (from c in db.UserCountries where c.UserID == user.ID select c.CountryID).ToArray()
            };

            ViewBag.RoleList = new SelectList(_manager.AllowedRoles(User), "ID", "FullName", model.userCredentials.RoleID);
            ViewBag.CountriesList = new SelectList(_manager.AllowedCountries(User), "ID", "FullName", model.CountryID);

            return View(model);
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Edit(UserViewModel model)
        {
            try
            {
                if (ModelState.IsValid)
                {
                    if (_manager.EditUser(model, User.ID))
                    {
                        return RedirectToAction("Index");
                    }
                }
            }
            catch (Exception ex)
            {
                HandleException(ex, model);
            }

            ViewBag.RoleList = new SelectList(_manager.AllowedRoles(User), "ID", "FullName", model.userCredentials.RoleID);
            ViewBag.CountriesList = new SelectList(_manager.AllowedCountries(User), "ID", "FullName", model.CountryID);

            return View(model);
        }

        [RoleSecurity(Permissions.Users, PermissionType.DELETE)]
        public ActionResult MarkInactive(int id = 0)
        {
            var userCredentials = db.UserCredentials.Find(id);
            if (userCredentials == null)
                return HttpNotFound();

            UserInfo user = db.UserInfoes.Find(userCredentials.UserID);

            if (_manager.MarkInactive(user.ID))
            {
                return RedirectToAction("Index");
            }

            return RedirectToAction("Index");
        }

        public ActionResult ChangePassword(int id = 0)
        {
            var userCredentials = db.UserCredentials.Find(id);
            if (userCredentials == null)
                return HttpNotFound();

            var model = new ResetPasswordViewModel();
            model.ID = userCredentials.ID;

            return View(model);
        }

        [HttpPost]
        public ActionResult ChangePassword(ResetPasswordViewModel model)
        {
            UserCredential userCredentials = db.UserCredentials.Find(model.ID);
            if (userCredentials == null)
                return HttpNotFound();
            try
            {
                if (ModelState.IsValid)
                {
                    if (_manager.ChangePassword(model))
                    {
                        return RedirectToAction("Index");

                    }
                }
            }
            catch (Exception ex)
            {
                HandleException(ex, model);
            }

            return View(model);
        }
    }
}