using IR2S.Common;
using IR2S.Common.Languages;
using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using System.Data.Entity;
using System.Globalization;
using System.Web;
using System.Web.Security;

namespace IR2S.Admin.Models
{
    public class UserViewModel : ModelMessage
    {
        public UserInfo userInfo { get; set; }
        public UserCredential userCredentials { get; set; }

        [Display(Name = "Password", ResourceType = typeof(Resource))]
        [Required(ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordRequired")]
        [RegularExpression(@"(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s)[0-9a-zA-Z!@#$%^&*()]*$", ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordCondition")]
        // ErrorMessage = "Password must be greater than 7 character and contains at least 1 lowercase letter, 1 uppercase letter and 1 digit")]
        [MinLength(6, ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordLength")]
        public string PASSWORD { get; set; }

        [Display(Name = "ConfirmPassword", ResourceType = typeof(Resource))]
        [Required(ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordRequired")]
        [MinLength(6, ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordLength")]
        [RegularExpression(@"(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s)[0-9a-zA-Z!@#$%^&*()]*$", ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordCondition")]
        [Compare("PASSWORD", ErrorMessageResourceType = typeof(Resource),
                      ErrorMessageResourceName = "MatchPassword")]
        public string CONFIRMPASSWORD { get; set; }

        [Display(Name = "Country", ResourceType = typeof(Resource))]
        [Required(ErrorMessageResourceType = typeof(Resource),
              ErrorMessageResourceName = "CountryRequired")]
        public int[] CountryID { get; set; }

        [Display(Name = "Picture", ResourceType = typeof(Resource))]
        public byte[] Image { get; set; }
    }

    public class ResetPasswordViewModel
    {
        public int ID { get; set; }
        public string guid { get; set; }

        [Display(Name = "Password", ResourceType = typeof(Resource))]
        [Required(ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordRequired")]
        [RegularExpression(@"(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s)[0-9a-zA-Z!@#$%^&*()]*$", ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordCondition")]
        [MinLength(6, ErrorMessageResourceType = typeof(Resource),
                       ErrorMessageResourceName = "PasswordLength")]
        [DataType(DataType.Password)]
        public string Password { get; set; }

        [Display(Name = "ConfirmPassword", ResourceType = typeof(Resource))]
        [DataType(DataType.Password)]
        [Required(ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordRequired")]
        [MinLength(6, ErrorMessageResourceType = typeof(Resource),
                       ErrorMessageResourceName = "PasswordLength")]
        [RegularExpression(@"(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s)[0-9a-zA-Z!@#$%^&*()]*$", ErrorMessageResourceType = typeof(Resource), ErrorMessageResourceName = "PasswordCondition")]
        [Compare("Password", ErrorMessageResourceType = typeof(Resource),
                      ErrorMessageResourceName = "MatchPassword")]
        public string ConfirmPassword { get; set; }

    }
}


