import moduleUser from '@modules/User/Resources/components/lang/nl/_index';

import LangDefaults from './_default.json';
import validations from './validations.json';
import fields from './fields.json';

// import firewall from './vendor/firewall/_index'

// import auth from './test/level/auth.json'
// import actions from './actions.json'
// import auth from './auth.json'

export default {
  ...LangDefaults,
  fields,
  validations,
  modules: {
    user: moduleUser,
  },
  // actions,
  // auth,
  // test : {
  //   level : {
  //     auth
  //   }
  // },
  // vendor : {
  //   firewall
  // }
};
