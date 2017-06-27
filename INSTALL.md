# Installation guide

## Production

Deploy Shepherd directly via the OpenShift UI.
Login as admin and Import the Shepherd OpenShift deployment template globally
```bash
oc login -u system:admin
oc create -f shepherd-openshift.yaml -n openshift
```
You can now click Add to project in the OpenShift ui to deploy Shepherd directly.


