# AGI-Bicom-Systems
IVR application


Task:
The application is a simple IVR which, when called, will play some greeting message, ask for a password if it is set and then prompt the user to enter one of the three available options.
Option 1: A sound file that is played prompts the user to enter an Extension number.  By entering an Extension number, a call is made to that Extension. (Ex. If ‘100’ is typed in, the Extension ‘100’ must be called). The user must not be able to call himself/herself. If the user tries to call himself/herself, a sound file should be played indicating an invalid action.

Option 2: A sound file that is played prompts the user to enter a password. The password can be a 4-digit number. This password will be used next time the user enters the IVR. If the user already has a password set when choosing this option, then s(he) will need to enter his/her old password before changing it to a new one. If a wrong password is entered, a proper sound file should be played and the user will not be able to enter a new password. The new password will have to be entered twice for confirmation. If the passwords do not match, a proper sound file should be played. A sound file should also be played on a successful password set/change.

Option 3: Choosing this option will do a loopback as if the IVR was just called. The greeting message will be played again and one of the options can be chosen again.
