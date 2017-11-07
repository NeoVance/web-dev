export default function emailsMatch() {
    let authority = '';
    let nonauthority = '';
    
    let compare = function() {
        if (nonauthority !== '') {
            return authority === nonauthority;
        }
        return false;
    }
    
    let email = function(value) {
        let r = /(.*)@(.*)/.exec(value);
        if (r === null) {
            return false;
        }
        
        return true;
    }
    
    return {
        setAuthority: function(value) {
            authority = value;
        },
        
        validate: function(value) {
            nonauthority = value;
            if (email(nonauthority)) {
                return compare();
            }
            return false;
        }
    };
}
